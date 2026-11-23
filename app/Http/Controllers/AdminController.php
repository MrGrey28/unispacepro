<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Curso;
use App\Models\Salon;
use App\Models\Usuario;
use App\Models\Ticket;
use App\Models\Funcion;
use App\Models\Butaca;
use App\Mail\ReservaConfirmada;

class AdminController extends Controller
{
    private function verificarAdmin(): void
    {
        if (!Auth::check() || !Auth::user()->esAdmin()) {
            abort(403, 'Acceso restringido únicamente al Administrador.');
        }
    }

    // 1. Aprobar solicitud de pago móvil del Auditorio
    public function aprobarTicket($ticket_id)
    {
        $this->verificarAdmin();

        $ticket = Ticket::with(['usuario', 'funcion.curso.salon', 'funcion.curso.profesor', 'butaca'])->findOrFail($ticket_id);
        $ticket->estado = 'activo';
        $ticket->save();

        try {
            \App\Services\CorreoService::enviar(
                $ticket->usuario->email,
                new ReservaConfirmada($ticket),
                $ticket->usuario->nombreCompleto()
            );
        } catch (\Exception $e) {
            Log::error('Error enviando correo tras aprobación admin: ' . $e->getMessage());
        }

        return redirect()->route('dashboard', ['tab' => 'pagos'])
            ->with('success', 'Pago verificado y butaca ' . $ticket->butaca->etiqueta . ' asignada a ' . $ticket->usuario->nombreCompleto() . '. Se ha enviado el correo de confirmación con QR.');
    }

    // 2. Rechazar solicitud de pago móvil y liberar la butaca
    public function rechazarTicket($ticket_id)
    {
        $this->verificarAdmin();

        $ticket = Ticket::with(['usuario', 'butaca'])->findOrFail($ticket_id);
        $etiqueta = $ticket->butaca->etiqueta ?? '';
        $nombre = $ticket->usuario ? $ticket->usuario->nombreCompleto() : '';
        $ticket->delete();

        return redirect()->route('dashboard', ['tab' => 'pagos'])
            ->with('success', 'Solicitud rechazada. La butaca ' . $etiqueta . ' de ' . $nombre . ' ha sido liberada nuevamente.');
    }

    // 3. Asignación manual de butaca en el Auditorio (para pagos recibidos vía Bot de WhatsApp)
    public function asignarManual(Request $request)
    {
        $this->verificarAdmin();

        $request->validate([
            'usuario_id'      => 'required|exists:usuarios,id',
            'funcion_id'      => 'required|exists:funciones,id',
            'butaca_id'       => 'required|exists:butacas,id',
            'referencia_pago' => 'required|string|max:100',
            'telefono_pago'   => 'nullable|string|max:30',
        ]);

        $usuario = Usuario::findOrFail($request->usuario_id);
        $funcion = Funcion::with('curso')->findOrFail($request->funcion_id);
        $butaca  = Butaca::findOrFail($request->butaca_id);

        $yaTiene = Ticket::where('usuario_id', $usuario->id)
            ->where('funcion_id', $funcion->id)
            ->whereIn('estado', ['activo', 'pendiente'])
            ->exists();

        if ($yaTiene) {
            return redirect()->route('dashboard', ['tab' => 'pagos'])
                ->with('error', 'El usuario ' . $usuario->nombreCompleto() . ' ya posee una butaca asignada o pendiente para este evento.');
        }

        $ocupada = Ticket::where('funcion_id', $funcion->id)
            ->where('butaca_id', $butaca->id)
            ->whereIn('estado', ['activo', 'pendiente'])
            ->exists();

        if ($ocupada) {
            return redirect()->route('dashboard', ['tab' => 'pagos'])
                ->with('error', 'La butaca ' . $butaca->etiqueta . ' ya se encuentra ocupada en este evento.');
        }

        $codigoTicket = Ticket::generarCodigo(
            $usuario->cedula,
            $butaca->etiqueta,
            $funcion->curso->codigo,
            $funcion->hora_inicio
        );

        $ticket = Ticket::create([
            'usuario_id'      => $usuario->id,
            'funcion_id'      => $funcion->id,
            'butaca_id'       => $butaca->id,
            'codigo_ticket'   => $codigoTicket,
            'estado'          => 'activo',
            'referencia_pago' => $request->referencia_pago,
            'telefono_pago'   => $request->telefono_pago ?: 'Taquilla Presencial',
            'banco_pago'      => 'Asignación Manual / Taquilla Presencial',
            'monto_pagado'    => $funcion->curso->precio ?? 0,
        ]);

        try {
            \App\Services\CorreoService::enviar(
                $usuario->email,
                new ReservaConfirmada($ticket),
                $usuario->nombreCompleto()
            );
        } catch (\Exception $e) {
            Log::error('Error enviando correo en asignación manual: ' . $e->getMessage());
        }

        return redirect()->route('dashboard', ['tab' => 'pagos'])
            ->with('success', 'Butaca ' . $butaca->etiqueta . ' asignada manualmente a ' . $usuario->nombreCompleto() . ' (Ticket: ' . $codigoTicket . '). Correo enviado con QR y enlace a calendario.');
    }

    // 4. Actualizar un Espacio / Salón en tiempo real (estado disponible/cerrado, equipos, foto, etc.)
    public function updateSalon(Request $request, $salon_id)
    {
        $this->verificarAdmin();

        $salon = Salon::findOrFail($salon_id);

        $request->validate([
            'nombre'  => 'required|string|max:120',
            'codigo'  => 'required|string|max:30',
            'estado'  => 'required|in:disponible,en_uso,mantenimiento',
            'equipos' => 'nullable|string|max:500',
            'foto'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $salon->nombre  = $request->nombre;
        $salon->codigo  = $request->codigo;
        $salon->estado  = $request->estado;
        $salon->equipos = $request->equipos;

        if ($request->hasFile('foto')) {
            @mkdir(public_path('images/salones'), 0777, true);
            $filename = 'salon_' . $salon->id . '_' . time() . '.' . $request->file('foto')->getClientOriginalExtension();
            $request->file('foto')->move(public_path('images/salones'), $filename);
            $salon->foto = 'images/salones/' . $filename;
        }

        $salon->save();

        return redirect()->route('dashboard', ['tab' => 'espacios'])
            ->with('success', 'El espacio "' . $salon->nombre . '" (' . $salon->codigo . ') ha sido actualizado en tiempo real.');
    }

    // 5. Actualizar un Curso, Taller o Evento en tiempo real
    public function updateCurso(Request $request, $curso_id)
    {
        $this->verificarAdmin();

        $curso = Curso::findOrFail($curso_id);

        $request->validate([
            'nombre'                    => 'required|string|max:150',
            'codigo'                    => 'required|string|max:30',
            'tipo'                      => 'required|in:curso,taller,clase,evento',
            'profesor_id'               => 'required|exists:usuarios,id',
            'salon_id'                  => 'required|exists:salones,id',
            'descripcion'               => 'nullable|string|max:2000',
            'semanas_totales'           => 'required|integer|min:1|max:52',
            'precio'                    => 'nullable|numeric|min:0',
            'activo'                    => 'required|boolean',
            'foto'                      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'funciones'                 => 'nullable|array',
            'funciones.*.dia_semana'    => 'required_with:funciones|in:lunes,martes,miercoles,jueves,viernes',
            'funciones.*.hora_inicio'   => 'required_with:funciones',
            'funciones.*.hora_fin'      => 'required_with:funciones',
            'nuevo_dia_semana'          => 'nullable|in:lunes,martes,miercoles,jueves,viernes',
            'nueva_hora_inicio'         => 'nullable',
            'nueva_hora_fin'            => 'nullable',
        ]);

        if ($request->filled('funciones')) {
            foreach ($request->funciones as $horarioData) {
                if (strtotime($horarioData['hora_fin']) <= strtotime($horarioData['hora_inicio'])) {
                    return redirect()->route('dashboard', ['tab' => 'cursos'])
                        ->with('error', 'La hora de fin debe ser posterior a la hora de inicio en "' . $curso->nombre . '".');
                }
            }
        }

        if ($request->filled('nuevo_dia_semana') && $request->filled('nueva_hora_inicio') && $request->filled('nueva_hora_fin')) {
            if (strtotime($request->nueva_hora_fin) <= strtotime($request->nueva_hora_inicio)) {
                return redirect()->route('dashboard', ['tab' => 'cursos'])
                    ->with('error', 'La hora de fin debe ser posterior a la hora de inicio en "' . $curso->nombre . '".');
            }
        }

        $salon = Salon::findOrFail($request->salon_id);

        $curso->nombre          = $request->nombre;
        $curso->codigo          = $request->codigo;
        $curso->tipo            = $request->tipo;
        $curso->profesor_id     = $request->profesor_id;
        $curso->salon_id        = $salon->id;
        $curso->descripcion     = $request->descripcion;
        $curso->semanas_totales = $request->semanas_totales;
        $curso->precio          = $request->precio ?? 0;
        $curso->activo          = (bool) $request->activo;

        if ($request->hasFile('foto')) {
            @mkdir(public_path('images/cursos'), 0777, true);
            $filename = 'curso_' . $curso->id . '_' . time() . '.' . $request->file('foto')->getClientOriginalExtension();
            $request->file('foto')->move(public_path('images/cursos'), $filename);
            $curso->foto = 'images/cursos/' . $filename;
        }

        $curso->save();

        if ($request->filled('funciones')) {
            foreach ($request->funciones as $funcionId => $horarioData) {
                $funcion = Funcion::where('curso_id', $curso->id)->find($funcionId);
                if ($funcion) {
                    $funcion->dia_semana  = $horarioData['dia_semana'];
                    $funcion->hora_inicio = strlen($horarioData['hora_inicio']) === 5 ? $horarioData['hora_inicio'] . ':00' : $horarioData['hora_inicio'];
                    $funcion->hora_fin    = strlen($horarioData['hora_fin']) === 5 ? $horarioData['hora_fin'] . ':00' : $horarioData['hora_fin'];
                    $funcion->cupo_maximo = $salon->capacidad;
                    $funcion->save();
                }
            }
        } elseif ($request->filled('nuevo_dia_semana') && $request->filled('nueva_hora_inicio') && $request->filled('nueva_hora_fin')) {
            Funcion::create([
                'curso_id'    => $curso->id,
                'dia_semana'  => $request->nuevo_dia_semana,
                'hora_inicio' => strlen($request->nueva_hora_inicio) === 5 ? $request->nueva_hora_inicio . ':00' : $request->nueva_hora_inicio,
                'hora_fin'    => strlen($request->nueva_hora_fin) === 5 ? $request->nueva_hora_fin . ':00' : $request->nueva_hora_fin,
                'cupo_maximo' => $salon->capacidad,
            ]);
        }

        return redirect()->route('dashboard', ['tab' => 'cursos'])
            ->with('success', 'La información y el horario de "' . $curso->nombre . '" se actualizaron en tiempo real.');
    }

    // 6. Crear un nuevo Curso, Taller o Evento del Auditorio
    public function storeCurso(Request $request)
    {
        $this->verificarAdmin();

        $request->validate([
            'nombre'          => 'required|string|max:150',
            'codigo'          => 'required|string|max:30|unique:cursos,codigo',
            'tipo'            => 'required|in:curso,taller,clase,evento',
            'profesor_id'     => 'required|exists:usuarios,id',
            'salon_id'        => 'required|exists:salones,id',
            'descripcion'     => 'nullable|string|max:2000',
            'fecha_inicio'    => 'nullable|date',
            'fecha_fin'       => 'nullable|date',
            'semanas_totales' => 'nullable|integer|min:1|max:52',
            'precio'          => 'nullable|numeric|min:0',
            'dia_semana'      => 'required|in:lunes,martes,miercoles,jueves,viernes',
            'hora_inicio'     => 'required',
            'hora_fin'        => 'required|after:hora_inicio',
            'foto'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $salon = Salon::findOrFail($request->salon_id);
        $fotoPath = $request->tipo === 'evento' ? 'images/cursos/graduacion.jpg' : 'images/carousel/edificio_nautica.jpg';

        if ($request->hasFile('foto')) {
            @mkdir(public_path('images/cursos'), 0777, true);
            $filename = 'curso_nuevo_' . time() . '.' . $request->file('foto')->getClientOriginalExtension();
            $request->file('foto')->move(public_path('images/cursos'), $filename);
            $fotoPath = 'images/cursos/' . $filename;
        }

        $semanas = (int) ($request->semanas_totales ?: ($request->tipo === 'evento' ? 1 : 6));
        $fechaInicio = $request->fecha_inicio ?: '2026-11-30';
        $fechaFin = $request->fecha_fin ?: \Carbon\Carbon::parse($fechaInicio)->addWeeks($semanas)->toDateString();

        $curso = Curso::create([
            'nombre'          => $request->nombre,
            'codigo'          => strtoupper($request->codigo),
            'tipo'            => $request->tipo,
            'profesor_id'     => $request->profesor_id,
            'salon_id'        => $salon->id,
            'descripcion'     => $request->descripcion,
            'foto'            => $fotoPath,
            'fecha_inicio'    => $fechaInicio,
            'fecha_fin'       => $fechaFin,
            'semanas_totales' => $semanas,
            'precio'          => $request->precio ?? 0,
            'activo'          => true,
        ]);

        Funcion::create([
            'curso_id'    => $curso->id,
            'dia_semana'  => $request->dia_semana,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin'    => $request->hora_fin,
            'cupo_maximo' => $salon->capacidad,
        ]);

        return redirect()->route('dashboard', ['tab' => 'cursos'])
            ->with('success', 'Se creó "' . $curso->nombre . '" exitosamente con su horario y salón asignado.');
    }

    // 7. Actualizar datos de un Profesor en tiempo real
    public function updateProfesor(Request $request, $profesor_id)
    {
        $this->verificarAdmin();

        $profesor = Usuario::where('rol_id', 4)->findOrFail($profesor_id);

        $request->validate([
            'nombre'      => 'required|string|max:50',
            'apellido'    => 'required|string|max:50',
            'email'       => 'required|email|max:255',
            'telefono'    => 'nullable|string|max:20',
            'biografia'   => 'nullable|string|max:1500',
            'gustos'      => 'nullable|string|max:300',
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        if ($request->hasFile('foto_perfil')) {
            if ($profesor->foto_perfil) {
                $oldDirect = public_path($profesor->foto_perfil);
                if (file_exists($oldDirect)) {
                    @unlink($oldDirect);
                }
                $oldStorage = public_path('storage/' . $profesor->foto_perfil);
                if (file_exists($oldStorage)) {
                    @unlink($oldStorage);
                }
            }
            $perfilesDir = public_path('images/perfiles');
            if (!is_dir($perfilesDir)) {
                @mkdir($perfilesDir, 0777, true);
            }
            $ext = $request->file('foto_perfil')->extension() ?: $request->file('foto_perfil')->getClientOriginalExtension() ?: 'jpg';
            $filename = 'perfil_' . $profesor->id . '_' . time() . '.' . $ext;
            $request->file('foto_perfil')->move($perfilesDir, $filename);

            $profesor->foto_perfil = 'images/perfiles/' . $filename;
        }

        $profesor->nombre    = $request->nombre;
        $profesor->apellido  = $request->apellido;
        $profesor->email     = $request->email;
        $profesor->telefono  = $request->telefono;
        $profesor->biografia = $request->biografia;
        $profesor->gustos    = $request->gustos;

        $profesor->save();

        return redirect()->route('dashboard', ['tab' => 'profesores'])
            ->with('success', 'Los datos del Prof. ' . $profesor->nombreCompleto() . ' han sido actualizados en tiempo real.');
    }

    // 8. Crear un nuevo Profesor desde el Panel Admin
    public function storeProfesor(Request $request)
    {
        $this->verificarAdmin();

        $request->validate([
            'nombre'    => 'required|string|max:50',
            'apellido'  => 'required|string|max:50',
            'cedula'    => 'required|string|max:12|unique:usuarios,cedula',
            'email'     => 'required|email|max:255|unique:usuarios,email',
            'telefono'  => 'nullable|string|max:20',
            'biografia' => 'nullable|string|max:1500',
            'gustos'      => 'nullable|string|max:300',
            'password'    => 'nullable|string|min:8|max:20',
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $profesor = Usuario::create([
            'rol_id'    => 4,
            'nombre'    => $request->nombre,
            'apellido'  => $request->apellido,
            'cedula'    => $request->cedula,
            'email'     => $request->email,
            'telefono'  => $request->telefono,
            'biografia' => $request->biografia,
            'gustos'    => $request->gustos,
            'password'  => Hash::make($request->password ?: 'Prof1234!'),
            'activo'    => true,
        ]);

        if ($request->hasFile('foto_perfil')) {
            $perfilesDir = public_path('images/perfiles');
            if (!is_dir($perfilesDir)) {
                @mkdir($perfilesDir, 0777, true);
            }
            $ext = $request->file('foto_perfil')->extension() ?: $request->file('foto_perfil')->getClientOriginalExtension() ?: 'jpg';
            $filename = 'perfil_' . $profesor->id . '_' . time() . '.' . $ext;
            $request->file('foto_perfil')->move($perfilesDir, $filename);

            $profesor->foto_perfil = 'images/perfiles/' . $filename;
            $profesor->save();
        }

        return redirect()->route('dashboard', ['tab' => 'profesores'])
            ->with('success', 'Se registró al Prof. ' . $profesor->nombreCompleto() . ' exitosamente.');
    }
}

