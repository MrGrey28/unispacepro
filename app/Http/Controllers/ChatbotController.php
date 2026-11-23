<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\Salon;
use App\Models\Usuario;

class ChatbotController extends Controller
{
    /**
     * Responde preguntas frecuentes exclusivamente sobre la plataforma UniSpacePro.
     */
    public function responder(Request $request)
    {
        $request->validate([
            'mensaje' => 'required|string|max:300',
        ]);

        $textoOriginal = trim($request->mensaje);
        $texto = $this->normalizar($textoOriginal);

        // Saludos básicos
        if ($this->contieneAlguno($texto, ['hola', 'buenos dias', 'buenas tardes', 'buenas noches', 'saludos', 'hey', 'que tal']) && mb_strlen($texto) < 35) {
            return response()->json([
                'respuesta' => "¡Hola! Soy el asistente virtual de UniSpacePro (UMC). Solo respondo dudas sobre nuestra página: cursos, talleres, eventos del Auditorio, espacios, profesores, reservas y perfil. ¿En qué te ayudo?",
                'sugerencias' => ['¿Cómo reservo un cupo?', 'Ver cursos disponibles', 'Eventos del Auditorio', '¿Cómo subir foto de perfil?'],
            ]);
        }

        // Agradecimientos / despedida
        if ($this->contieneAlguno($texto, ['gracias', 'muchas gracias', 'listo', 'perfecto', 'adios', 'chao']) && mb_strlen($texto) < 35) {
            return response()->json([
                'respuesta' => "¡Con gusto! Si tienes otra duda sobre UniSpacePro, aquí estoy para ayudarte.",
                'sugerencias' => ['¿Cómo reservo un cupo?', 'Ver espacios', 'Ver profesores'],
            ]);
        }

        // 1. Cómo reservar / inscribirse / butacas
        if ($this->contieneAlguno($texto, ['reservar', 'reserva', 'inscribir', 'inscripcion', 'cupo', 'butaca', 'asiento', 'mapa'])) {
            return response()->json([
                'respuesta' => "Para reservar un cupo o butaca en UniSpacePro:\n1. Inicia sesión con tu cuenta.\n2. Entra en «Cursos y Talleres» (gratuitos) o «Auditorio y Eventos» (de pago).\n3. Haz clic en «Seleccionar Butaca en el Mapa».\n4. Elige una butaca disponible en verde y confirma tu reserva.\nTu ticket con código único quedará en «Dashboard».",
                'sugerencias' => ['¿Cómo pago en el Auditorio?', '¿Cómo cancelo una reserva?', '¿Cómo me registro?'],
            ]);
        }

        // 2. Cancelar reserva / ticket
        if ($this->contieneAlguno($texto, ['cancelar', 'anular', 'eliminar ticket', 'quitar reserva', 'ticket'])) {
            return response()->json([
                'respuesta' => "Puedes ver y cancelar tus reservas activas ingresando a tu «Dashboard» en el menú superior. Allí encontrarás tus tickets con código, salón, horario y el botón para cancelar y liberar la butaca.",
                'sugerencias' => ['¿Cómo reservo un cupo?', 'Eventos del Auditorio'],
            ]);
        }

        // 3. Auditorio / eventos / pagos / pago móvil / efectivo
        if ($this->contieneAlguno($texto, ['auditorio', 'evento', 'pago', 'pagar', 'pago movil', 'efectivo', 'taquilla', 'presencial', 'graduacion', 'congreso', 'simposio', 'qr'])) {
            $eventos = Curso::where('tipo', 'evento')->where('activo', true)->get(['nombre', 'precio']);
            $lista = $eventos->map(fn ($e) => "• {$e->nombre} ($" . number_format((float) $e->precio, 2) . " USD)")->implode("\n");

            return response()->json([
                'respuesta' => "En «Auditorio y Eventos» puedes reservar butacas (A1 a H12) para eventos especiales:\n" . ($lista ?: "• Consulta la sección Auditorio y Eventos.") . "\n\nPuedes pagar por Pago Móvil (0212-3500100) o en Efectivo. Para terminar de confirmar tu pago o solventar cualquier inconveniente, debes acudir de manera presencial a la taquilla administrativa de la UMC.",
                'sugerencias' => ['¿Cómo reservo un cupo?', '¿Dónde veo mis tickets?'],
            ]);
        }

        // 4. Cursos y talleres
        if ($this->contieneAlguno($texto, ['curso', 'taller', 'clase', 'horario', 'gratis', 'gratuito', 'catalogo'])) {
            $total = Curso::where('activo', true)->whereIn('tipo', ['curso', 'taller', 'clase'])->count();
            $ejemplos = Curso::where('activo', true)->whereIn('tipo', ['curso', 'taller', 'clase'])->limit(5)->pluck('nombre')->implode(', ');

            return response()->json([
                'respuesta' => "Actualmente tenemos {$total} cursos y talleres gratuitos activos (como: {$ejemplos}, entre otros). Entra en la pestaña «Cursos y Talleres» para filtrar por profesor, modalidad o horario y reservar tu butaca.",
                'sugerencias' => ['¿Cómo reservo un cupo?', 'Ver profesores', 'Eventos del Auditorio'],
            ]);
        }

        // 5. Espacios / salones / laboratorios / aulas
        if ($this->contieneAlguno($texto, ['espacio', 'salon', 'salones', 'aula', 'laboratorio', 'multimedia', 'edificio', 'bloque'])) {
            $disponibles = Salon::where('estado', 'disponible')->count();
            $total = Salon::count();

            return response()->json([
                'respuesta' => "Contamos con {$total} espacios registrados ({$disponibles} disponibles en este momento), incluyendo aulas, laboratorios de Física/Estructuras, salas Multimedia y el Auditorio Principal UMC. Puedes consultar su capacidad, equipos y estado en la sección «Espacios».",
                'sugerencias' => ['Cursos disponibles', 'Eventos del Auditorio'],
            ]);
        }

        // 6. Profesores / docentes
        if ($this->contieneAlguno($texto, ['profesor', 'profesores', 'docente', 'docentes', 'ponente', 'maestro'])) {
            $totalProf = Usuario::where('rol_id', 4)->where('activo', true)->count();

            return response()->json([
                'respuesta' => "Tenemos {$totalProf} profesores especialistas registrados en UniSpacePro. En la sección «Profesores» puedes buscarlos por nombre, especialidad o curso que imparten y desplegar su ficha completa.",
                'sugerencias' => ['¿Cómo subir foto de perfil?', 'Cursos disponibles'],
            ]);
        }

        // 7. Foto de perfil / editar perfil
        if ($this->contieneAlguno($texto, ['perfil', 'foto', 'imagen', 'avatar', 'biografia', 'gustos', 'intereses', 'telefono'])) {
            return response()->json([
                'respuesta' => "Para actualizar tus datos o subir tu foto de perfil (JPG, PNG o WEBP de hasta 3 MB), inicia sesión y entra en «Mi Perfil» en la barra superior. Los profesores también tienen un acceso directo «Editar Perfil / Foto» desde su Dashboard.",
                'sugerencias' => ['¿Cómo cambio mi contraseña?', 'Ver profesores'],
            ]);
        }

        // 8. Registro, login, contraseña, captcha
        if ($this->contieneAlguno($texto, ['registro', 'registrar', 'crear cuenta', 'cuenta', 'login', 'iniciar sesion', 'entrar', 'contrasena', 'clave', 'olvide', 'recuperar', 'captcha'])) {
            return response()->json([
                'respuesta' => "• Registro: Entra en «Registrarse» con tu nombre, apellido, cédula (solo números), correo y una contraseña de 8 a 20 caracteres (con minúsculas, mayúsculas y números).\n• Login: Ingresa correo, contraseña y el código CAPTCHA.\n• Recuperar clave: Usa «¿Olvidaste tu contraseña?» en la pantalla de Iniciar Sesión.",
                'sugerencias' => ['¿Cómo reservo un cupo?', '¿Cómo subir foto de perfil?'],
            ]);
        }

        // 9. Ubicación / Universidad / Contacto / Qué es UniSpace
        if ($this->contieneAlguno($texto, ['unispace', 'universidad', 'umc', 'donde queda', 'ubicacion', 'direccion', 'que es', 'para que sirve', 'ayuda', 'contacto'])) {
            return response()->json([
                'respuesta' => "UniSpacePro es el Sistema Integral de Reserva de Cupos y Espacios de la Universidad Marítima del Caribe (UMC), ubicada en Av. El Ejército, Catia la Mar, Edo. La Guaira, Venezuela. Desde aquí puedes consultar espacios, profesores, inscribirte en cursos/talleres y reservar butacas del Auditorio.",
                'sugerencias' => ['Cursos disponibles', 'Eventos del Auditorio', '¿Cómo me registro?'],
            ]);
        }

        // Pregunta fuera del alcance de la página
        return response()->json([
            'respuesta' => "Lo siento, solo puedo responder preguntas fáciles relacionadas con la página UniSpacePro (cursos, talleres, Auditorio y pagos, espacios, profesores, reservas, perfil o acceso). Prueba con una de estas opciones:",
            'sugerencias' => ['¿Cómo reservo un cupo?', 'Cursos disponibles', 'Eventos del Auditorio', '¿Cómo me registro?'],
        ]);
    }

    private function normalizar(string $texto): string
    {
        $texto = mb_strtolower($texto, 'UTF-8');
        $reemplazos = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n',
        ];
        return strtr($texto, $reemplazos);
    }

    private function contieneAlguno(string $texto, array $palabras): bool
    {
        foreach ($palabras as $palabra) {
            if (str_contains($texto, $palabra)) {
                return true;
            }
        }
        return false;
    }
}

