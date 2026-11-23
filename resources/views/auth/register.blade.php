@extends('layouts.public')

@section('title', 'Registro')

@section('content')
<section class="auth-section">
    <div class="auth-container" style="max-width: 960px; padding: 0; overflow: hidden; display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));">
        <div style="background: linear-gradient(135deg, rgba(15,23,42,0.88), rgba(30,58,95,0.85)), url('{{ asset('images/carousel/areas_verdes.jpg') }}') center/cover no-repeat; color: white; padding: 3rem 2.5rem; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 2rem;">
                    <img src="{{ asset('images/logo.png') }}" alt="UniSpace Logo" style="height: 42px; width: auto;">
                    <strong style="font-size: 1.4rem; letter-spacing: -0.02em;">UniSpace</strong>
                </div>
                <h3 style="font-size: 1.65rem; color: #ffffff; margin-bottom: 0.85rem; line-height: 1.25;">Únete a la Comunidad Universitaria</h3>
                <p style="color: #cbd5e1; font-size: 0.95rem; line-height: 1.6;">Crea tu cuenta estudiantil para consultar horarios, inscribirte en cursos y talleres, y seleccionar tu butaca en el campus.</p>
            </div>
            <div style="border-top: 1px solid rgba(255,255,255,0.18); padding-top: 1.2rem; font-size: 0.82rem; color: #cbd5e1;">
                Universidad Marítima del Caribe • Sistema Integral de Espacios
            </div>
        </div>
        <div style="padding: 2.8rem 2.5rem;">
        <div class="auth-header" style="text-align: left; margin-bottom: 1.6rem;">
            <h2>Crear Cuenta</h2>
            <p>Únete a UniSpace y comienza a reservar tus espacios de estudio.</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="auth-form">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required maxlength="50" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+" title="Solo se permiten letras y espacios" oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]/g, '')">
                </div>
                <div class="form-group">
                    <label for="apellido">Apellido</label>
                    <input type="text" id="apellido" name="apellido" value="{{ old('apellido') }}" required maxlength="50" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+" title="Solo se permiten letras y espacios" oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]/g, '')">
                </div>
            </div>

            <div class="form-group">
                <label for="cedula">Cédula de Identidad</label>
                <input type="text" id="cedula" name="cedula" value="{{ old('cedula') }}" required placeholder="Ej: 28123456" maxlength="8" pattern="[0-9]+" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            </div>

            <div class="form-group">
                <label for="email">Correo Electrónico (Institucional o Personal de Google)</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required maxlength="255">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div style="position: relative;">
                        <input type="password" id="password" name="password" required minlength="8" maxlength="20" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,20}" title="Debe contener entre 8 y 20 caracteres, incluyendo minúsculas, mayúsculas y números" style="padding-right: 40px; width: 100%;">
                        <button type="button" id="togglePassword1" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #6c757d;" title="Mostrar/Ocultar Contraseña">
                            <svg id="eyeIcon1" viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                    <small style="display: block; margin-top: 0.35rem; font-size: 0.76rem; color: #64748b;">Entre 8 y 20 caracteres (minúsculas, mayúsculas y números).</small>
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirmar Contraseña</label>
                    <div style="position: relative;">
                        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" maxlength="20" style="padding-right: 40px; width: 100%;">
                        <button type="button" id="togglePassword2" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #6c757d;" title="Mostrar/Ocultar Contraseña">
                            <svg id="eyeIcon2" viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Verificación de seguridad CAPTCHA -->
            <div class="form-group" style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; padding: 0.9rem 1rem;">
                <label for="captcha" style="margin-bottom: 0.55rem; display: flex; justify-content: space-between; align-items: center;">
                    <span>Verificación de Seguridad (CAPTCHA)</span>
                    <button type="button" id="btn-refresh-captcha-reg" style="background: none; border: none; color: var(--secondary-color); font-size: 0.8rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.3rem;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                        Cambiar código
                    </button>
                </label>
                <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                    <img id="captcha-img-reg" src="{{ $captchaImg ?? '' }}" alt="Código CAPTCHA" style="height: 48px; width: 160px; border-radius: 8px; border: 1px solid #94a3b8; user-select: none;">
                    <input type="text" id="captcha" name="captcha" required maxlength="5" autocomplete="off" placeholder="Escribe los 5 caracteres" style="flex: 1; min-width: 140px; text-transform: uppercase; font-weight: 700; letter-spacing: 2px; text-align: center;">
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Registrarse</button>
        </form>

        <div class="auth-footer">
            <p>¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a></p>
        </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function setupToggle(toggleId, inputId, iconId) {
            const toggle = document.getElementById(toggleId);
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (toggle && input) {
                toggle.addEventListener('click', function () {
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    
                    if (type === 'text') {
                        icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
                    } else {
                        icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
                    }
                });
            }
        }
        
        setupToggle('togglePassword1', 'password', 'eyeIcon1');
        setupToggle('togglePassword2', 'password_confirmation', 'eyeIcon2');

        const refreshBtn = document.getElementById('btn-refresh-captcha-reg');
        const captchaImg = document.getElementById('captcha-img-reg');
        const captchaInput = document.getElementById('captcha');

        if (refreshBtn && captchaImg) {
            refreshBtn.addEventListener('click', function () {
                fetch('{{ route('captcha.refresh') }}')
                    .then(res => res.json())
                    .then(data => {
                        if (data.image) {
                            captchaImg.src = data.image;
                            if (captchaInput) {
                                captchaInput.value = '';
                                captchaInput.focus();
                            }
                        }
                    });
            });
        }
    });
</script>
@endpush
@endsection
