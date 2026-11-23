<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

// Este controlador se encarga de todo el flujo de acceso de los usuarios al sistema.
// Aquí manejamos el inicio de sesión, el registro de estudiantes, el cierre de sesión 
// y el proceso para recuperar la contraseña mediante un correo electrónico.
class AuthController extends Controller
{
    // Genera un código CAPTCHA seguro en sesión y retorna la imagen SVG codificada en base64.
    private function generarCaptchaSvg(): string
    {
        $caracteres = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $codigo = '';
        for ($i = 0; $i < 5; $i++) {
            $codigo .= $caracteres[random_int(0, strlen($caracteres) - 1)];
        }

        session(['captcha_code' => $codigo]);

        $letrasSvg = '';
        $colores = ['#ffffff', '#93c5fd', '#fde047', '#a7f3d0', '#f8fafc'];
        for ($i = 0; $i < 5; $i++) {
            $x = 22 + ($i * 26);
            $y = random_int(29, 36);
            $rot = random_int(-18, 18);
            $color = $colores[$i % count($colores)];
            $char = $codigo[$i];
            $letrasSvg .= "<text x=\"{$x}\" y=\"{$y}\" fill=\"{$color}\" font-family=\"monospace, sans-serif\" font-size=\"23\" font-weight=\"bold\" transform=\"rotate({$rot} {$x} {$y})\">{$char}</text>";
        }

        $lineasSvg = '';
        for ($l = 0; $l < 4; $l++) {
            $y1 = random_int(6, 44);
            $y2 = random_int(6, 44);
            $cy = random_int(4, 46);
            $lineasSvg .= "<path d=\"M 5 {$y1} Q 80 {$cy} 155 {$y2}\" stroke=\"rgba(147,197,253,0.38)\" stroke-width=\"1.5\" fill=\"none\"/>";
        }

        $puntosSvg = '';
        for ($p = 0; $p < 18; $p++) {
            $cx = random_int(6, 154);
            $cy = random_int(6, 44);
            $puntosSvg .= "<circle cx=\"{$cx}\" cy=\"{$cy}\" r=\"1.3\" fill=\"rgba(255,255,255,0.32)\"/>";
        }

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="160" height="48" viewBox="0 0 160 48">
    <defs>
        <linearGradient id="capBg" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#0f172a"/>
            <stop offset="100%" stop-color="#1e3a5f"/>
        </linearGradient>
    </defs>
    <rect width="160" height="48" rx="8" fill="url(#capBg)"/>
    {$lineasSvg}
    {$puntosSvg}
    {$letrasSvg}
</svg>
SVG;

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    // Endpoint AJAX para refrescar la imagen del CAPTCHA sin recargar toda la página.
    public function refreshCaptcha()
    {
        return response()->json([
            'image' => $this->generarCaptchaSvg(),
        ]);
    }

    // Carga la vista del formulario de inicio de sesión para que el usuario ingrese sus datos.
    public function showLoginForm()
    {
        $captchaImg = $this->generarCaptchaSvg();
        return view('auth.login', compact('captchaImg'));
    }

    // Procesa el intento de inicio de sesión.
    // Primero valida que se envíen el correo, la clave y el código CAPTCHA obligatoriamente.
    // Luego, utiliza la autenticación de Laravel para comprobar las credenciales.
    // Si todo es correcto, inicia la sesión, la regenera por seguridad y redirige al dashboard.
    // Si falla, devuelve al usuario al formulario mostrando un mensaje de error claro.
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email', 'max:255'],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:20',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            ],
            'captcha'  => ['required', 'string'],
        ], [
            'email.max'      => 'El correo electrónico no puede exceder los 255 caracteres.',
            'password.min'   => 'La contraseña debe tener un mínimo de 8 caracteres.',
            'password.max'   => 'La contraseña no puede exceder los 20 caracteres.',
            'password.regex' => 'La contraseña debe contener entre 8 y 20 caracteres, incluyendo minúsculas, mayúsculas y números.',
        ]);

        $captchaEsperado = session('captcha_code');
        if (!$captchaEsperado || strtoupper(trim($request->captcha)) !== strtoupper($captchaEsperado)) {
            return back()->withErrors([
                'captcha' => 'El código de verificación CAPTCHA no coincide. Por favor, inténtalo de nuevo.',
            ])->onlyInput('email');
        }

        session()->forget('captcha_code');

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    // Muestra la vista con el formulario para registrar una cuenta de estudiante.
    public function showRegistrationForm()
    {
        $captchaImg = $this->generarCaptchaSvg();
        return view('auth.register', compact('captchaImg'));
    }

    // Registra un nuevo estudiante en la base de datos.
    // Validamos que los campos obligatorios cumplan con los formatos correctos (cédula única, correo único, contraseña segura y CAPTCHA).
    // Creamos el registro del usuario encriptando la contraseña con Hash por seguridad y le asignamos el rol de estudiante (rol_id 3).
    // Una vez registrado, lo autenticamos de forma automática y lo redirigimos directo al dashboard.
    public function register(Request $request)
    {
        $request->validate([
            'nombre'   => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u'],
            'apellido' => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u'],
            'cedula'   => ['required', 'string', 'max:8', 'regex:/^[0-9]+$/', 'unique:usuarios'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:usuarios'],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:20',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                'confirmed',
            ],
            'captcha'  => ['required', 'string'],
        ], [
            'nombre.regex'   => 'El nombre solo puede contener letras y espacios.',
            'apellido.regex' => 'El apellido solo puede contener letras y espacios.',
            'email.max'      => 'El correo electrónico no puede exceder los 255 caracteres.',
            'password.min'   => 'La contraseña debe tener un mínimo de 8 caracteres.',
            'password.max'   => 'La contraseña no puede exceder los 20 caracteres.',
            'password.regex' => 'La contraseña debe contener entre 8 y 20 caracteres, incluyendo minúsculas, mayúsculas y números.',
        ]);

        $captchaEsperado = session('captcha_code');
        if (!$captchaEsperado || strtoupper(trim($request->captcha)) !== strtoupper($captchaEsperado)) {
            return back()->withErrors([
                'captcha' => 'El código de verificación CAPTCHA no coincide. Por favor, inténtalo de nuevo.',
            ])->withInput($request->except('password', 'password_confirmation', 'captcha'));
        }

        session()->forget('captcha_code');

        $usuario = Usuario::create([
            'nombre' => trim($request->nombre),
            'apellido' => trim($request->apellido),
            'cedula' => $request->cedula,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol_id' => 3,
            'activo' => true,
        ]);

        $correoEnviado = false;
        try {
            $verifyUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                'verification.verify.custom',
                now()->addHours(24),
                ['id' => $usuario->id, 'hash' => sha1($usuario->email)]
            );
            \Illuminate\Support\Facades\Mail::to($usuario->email)->send(new \App\Mail\RegistroVerificacionMail($usuario, $verifyUrl));
            $correoEnviado = true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error enviando correo de verificación al registrarse: ' . $e->getMessage());
        }

        Auth::login($usuario);

        return redirect()->route('dashboard')->with(
            $correoEnviado ? 'success' : 'info',
            $correoEnviado
                ? '¡Registro exitoso! Te enviamos un correo de verificación a ' . $usuario->email . '.'
                : '¡Registro exitoso! Tu cuenta fue creada (el servidor de correo no pudo enviar el mensaje en este momento).'
        );
    }

    public function verifyEmail(Request $request, $id, $hash)
    {
        if (!$request->hasValidSignature()) {
            return redirect()->route('home')->withErrors(['email' => 'El enlace de verificación no es válido o ha expirado.']);
        }

        $usuario = Usuario::findOrFail($id);
        if (!hash_equals((string) $hash, sha1($usuario->email))) {
            return redirect()->route('home')->withErrors(['email' => 'El enlace de verificación no corresponde a este usuario.']);
        }

        if (!$usuario->email_verificado_en) {
            $usuario->email_verificado_en = now();
            $usuario->save();
        }

        Auth::login($usuario);

        return redirect()->route('dashboard')->with('success', '¡Tu correo electrónico ha sido verificado correctamente!');
    }

    // Cierra la sesión activa del usuario.
    // Invalida la sesión actual del navegador y regenera el token CSRF para evitar
    // que la sesión previa pueda ser reutilizada de forma maliciosa.
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // Carga la vista del formulario donde el usuario puede solicitar restablecer su contraseña escribiendo su correo.
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    // Genera y envía el enlace de recuperación de contraseña.
    // Verifica si el correo ingresado pertenece a un usuario registrado.
    // Si existe, genera un token aleatorio seguro, lo guarda en la tabla 'tokens_reset_password'
    // y envía el correo con el enlace que contiene este token único.
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|max:255']);

        $user = Usuario::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No encontramos ningún usuario con ese correo electrónico.']);
        }

        $token = \Illuminate\Support\Str::random(60);

        \Illuminate\Support\Facades\DB::table('tokens_reset_password')->updateOrInsert(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => $token,
                'creado_en' => now()
            ]
        );

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\ResetPasswordMail($token, $user->email));
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'No se pudo enviar el correo de recuperación. Por favor intenta más tarde o verifica la configuración del servidor.']);
        }

        return back()->with('status', '¡Te hemos enviado un enlace para restablecer tu contraseña!');
    }

    // Muestra el formulario para escribir la nueva contraseña, pasando el token recibido desde la URL.
    public function showResetForm($token)
    {
        return view('auth.passwords.reset', ['token' => $token]);
    }

    // Guarda la nueva contraseña en el sistema.
    // Valida que el token y el correo coincidan con la base de datos para asegurar la identidad.
    // Si coinciden, actualiza la contraseña del usuario con encriptación Hash,
    // borra el token usado para que no se pueda volver a utilizar y lo redirige al login con éxito.
    public function reset(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email|max:255',
            'password' => [
                'required',
                'string',
                'min:8',
                'max:20',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                'confirmed',
            ],
        ], [
            'password.min'   => 'La contraseña debe tener un mínimo de 8 caracteres.',
            'password.max'   => 'La contraseña no puede exceder los 20 caracteres.',
            'password.regex' => 'La contraseña debe contener entre 8 y 20 caracteres, incluyendo minúsculas, mayúsculas y números.',
        ]);

        $record = \Illuminate\Support\Facades\DB::table('tokens_reset_password')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$record) {
            return back()->withErrors(['email' => 'El token de restablecimiento no es válido o ha expirado.']);
        }

        $user = Usuario::where('email', $request->email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        \Illuminate\Support\Facades\DB::table('tokens_reset_password')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Tu contraseña ha sido restablecida. Ya puedes iniciar sesión.');
    }
}
