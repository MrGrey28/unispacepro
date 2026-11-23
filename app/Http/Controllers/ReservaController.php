<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Funcion;
use App\Models\Salon;
use App\Models\Butaca;
use App\Models\Ticket;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ReservaConfirmada;

// Este controlador maneja todo el flujo de reservas de asientos (butacas) para las clases o eventos en los salones y el Auditorio.
class ReservaController extends Controller
{
    public function showMap($funcion_id)
    {
        $usuario = Auth::user();

        $funcion = Funcion::with(['curso.profesor', 'curso.funciones', 'tickets'])->findOrFail($funcion_id);
        $salon = Salon::findOrFail($funcion->curso->salon_id);

        $butacas = Butaca::where('salon_id', $salon->id)
                    ->orderBy('fila')
                    ->orderBy('columna')
                    ->get();

        $butacasOcupadas = $funcion->tickets()
                            ->whereIn('estado', ['activo', 'pendiente'])
                            ->pluck('butaca_id')
                            ->toArray();

        $butacasPendientes = $funcion->tickets()
                            ->where('estado', 'pendiente')
                            ->pluck('butaca_id')
                            ->toArray();

        $yaReservado = $funcion->tickets()
                        ->where('usuario_id', $usuario->id)
                        ->whereIn('estado', ['activo', 'pendiente'])
                        ->exists();

        return view('reserva.mapa', compact('funcion', 'salon', 'butacas', 'butacasOcupadas', 'butacasPendientes', 'yaReservado'));
    }

    public function store(Request $request, $funcion_id)
    {
        $usuario = Auth::user();

        $funcion = Funcion::with('curso.salon')->findOrFail($funcion_id);
        $salon = $funcion->curso->salon;

        if ($salon && !$salon->estaDisponible()) {
            return redirect()->back()->withErrors(['error' => 'Este espacio se encuentra actualmente cerrado o en mantenimiento por disposición del Administrador.']);
        }

        $rules = [
            'butaca_id' => 'required|exists:butacas,id',
        ];

        $esEventoPago = $funcion->curso->esEvento();
        $metodoPago = $request->input('metodo_pago', 'pago_movil');
        if ($esEventoPago) {
            $rules['metodo_pago'] = 'nullable|in:pago_movil,efectivo';
            if ($metodoPago !== 'efectivo') {
                $rules['banco_pago']      = 'required|string|max:80';
                $rules['telefono_pago']   = 'required|string|max:30';
                $rules['referencia_pago'] = 'required|string|min:4|max:100';
            }
        }

        $request->validate($rules);

        $butaca = Butaca::findOrFail($request->butaca_id);

        $yaReservado = Ticket::where('usuario_id', $usuario->id)
                            ->where('funcion_id', $funcion->id)
                            ->whereIn('estado', ['activo', 'pendiente'])
                            ->exists();
        
        if ($yaReservado) {
            return redirect()->back()->withErrors(['error' => 'Ya tienes una reserva o solicitud en proceso para este curso o evento.']);
        }

        $butacaOcupada = Ticket::where('funcion_id', $funcion->id)
                                ->where('butaca_id', $butaca->id)
                                ->whereIn('estado', ['activo', 'pendiente'])
                                ->exists();

        if ($butacaOcupada) {
            return redirect()->back()->withErrors(['error' => 'Lo sentimos, esta butaca acaba de ser reservada o solicitada por otro usuario.']);
        }

        $codigoTicket = Ticket::generarCodigo(
            $usuario->cedula, 
            $butaca->etiqueta, 
            $funcion->curso->codigo, 
            $funcion->hora_inicio
        );

        $esEfectivo = $esEventoPago && $metodoPago === 'efectivo';
        $referenciaFinal = $esEventoPago
            ? ($esEfectivo ? 'EFECTIVO-' . strtoupper(substr(uniqid(), -6)) : $request->referencia_pago)
            : null;
        $telefonoFinal = $esEventoPago
            ? ($esEfectivo ? 'Taquilla Presencial' : $request->telefono_pago)
            : null;
        $bancoFinal = $esEventoPago
            ? ($esEfectivo ? 'Efectivo (Taquilla Presencial UMC)' : $request->banco_pago)
            : null;

        try {
            $ticket = Ticket::create([
                'usuario_id'      => $usuario->id,
                'funcion_id'      => $funcion->id,
                'butaca_id'       => $butaca->id,
                'codigo_ticket'   => $codigoTicket,
                'estado'          => $esEventoPago ? 'pendiente' : 'activo',
                'referencia_pago' => $referenciaFinal,
                'telefono_pago'   => $telefonoFinal,
                'banco_pago'      => $bancoFinal,
                'monto_pagado'    => $esEventoPago ? $funcion->curso->precio : 0,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withErrors(['error' => 'Lo sentimos, este asiento ya fue reservado o ya posees una solicitud para este evento.']);
        }

        if ($esEventoPago) {
            $mensajeEvento = $esEfectivo
                ? 'Solicitud en Efectivo enviada (Butaca ' . $butaca->etiqueta . ' | Código: ' . $referenciaFinal . '). Acude presencialmente a la Taquilla Administrativa UMC para cancelar y confirmar tu pago.'
                : 'Solicitud enviada al Administrador (Butaca ' . $butaca->etiqueta . ' | Referencia Pago Móvil: ' . $referenciaFinal . '). Una vez verificado tu pago (o confirmado en taquilla presencial), se activará tu ticket con QR.';

            return redirect()->route('dashboard')->with('success', $mensajeEvento);
        }

        try {
            Mail::to($usuario->email)->send(new ReservaConfirmada($ticket));
        } catch (\Exception $e) {
            Log::error('Fallo al enviar correo de reserva: ' . $e->getMessage());
        }

        return redirect()->route('dashboard')->with('success', '¡Reserva exitosa! Se ha generado tu ticket ' . $codigoTicket);
    }

    public function cancelar($ticket_id)
    {
        $usuario = Auth::user();
        
        $ticket = Ticket::where('id', $ticket_id)
                        ->where('usuario_id', $usuario->id)
                        ->whereIn('estado', ['activo', 'pendiente'])
                        ->firstOrFail();

        $ticket->delete();

        return redirect()->route('dashboard')->with('success', 'Tu reserva o solicitud ha sido cancelada exitosamente.');
    }
}
