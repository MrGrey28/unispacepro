<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verifica tu correo - UniSpacePro</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f8fafc; color: #0f172a; padding: 24px;">
    <div style="max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden;">
        <div style="background: #1e3a5f; color: #ffffff; padding: 20px 24px;">
            <h2 style="margin: 0; font-size: 20px;">Bienvenido a UniSpacePro (UMC)</h2>
        </div>
        <div style="padding: 24px;">
            <p>Hola <strong>{{ $usuario->nombreCompleto() }}</strong>,</p>
            <p>Tu cuenta estudiantil ha sido creada exitosamente en UniSpacePro. Para verificar tu correo electrónico, haz clic en el siguiente botón:</p>
            <p style="text-align: center; margin: 28px 0;">
                <a href="{{ $verifyUrl }}" style="background: #2563eb; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: bold; display: inline-block;">
                    Verificar mi Correo
                </a>
            </p>
            <p style="font-size: 13px; color: #64748b;">Si el botón no funciona, copia y pega este enlace en tu navegador:</p>
            <p style="font-size: 12px; color: #2563eb; word-break: break-all;">{{ $verifyUrl }}</p>
            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;">
            <p style="font-size: 12px; color: #64748b; margin: 0;">Universidad Marítima del Caribe • Sistema Integral de Reserva de Espacios</p>
        </div>
    </div>
</body>
</html>
