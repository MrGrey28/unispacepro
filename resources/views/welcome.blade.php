@extends('layouts.public')

@section('title', 'Portal Universitario')

@push('scripts')
<style>
    @keyframes heroFadeUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .home-hero {
        position: relative;
        padding: 6rem 0 7rem 0;
        text-align: center;
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 58, 95, 0.85) 55%, rgba(29, 78, 216, 0.82) 100%), url("{{ asset('images/carousel/entrada_principal.jpg') }}") center/cover no-repeat;
        color: white;
        overflow: hidden;
    }

    .home-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px);
        background-size: 28px 28px;
        opacity: 0.45;
        pointer-events: none;
    }

    .home-hero-content {
        position: relative;
        z-index: 2;
        max-width: 780px;
        margin: 0 auto;
        animation: heroFadeUp 0.6s ease both;
    }

    .hero-pill {
        display: inline-block;
        padding: 0.4rem 1.1rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.22);
        color: #e0f2fe;
        font-size: 0.84rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        margin-bottom: 1.4rem;
    }

    .home-hero-content h1 {
        font-size: clamp(2.2rem, 4.5vw, 3.35rem);
        line-height: 1.15;
        margin-bottom: 1.2rem;
        color: #ffffff;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .home-hero-content p {
        font-size: 1.15rem;
        margin: 0 auto 2.2rem auto;
        max-width: 620px;
        color: #cbd5e1;
        line-height: 1.65;
    }

    .hero-cta-group {
        display: flex;
        justify-content: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .btn-hero-primary {
        background: #2563eb;
        color: #ffffff;
        padding: 0.95rem 2rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1rem;
        border: 2px solid #3b82f6;
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.35);
        transition: all 0.25s ease;
    }

    .btn-hero-primary:hover {
        background: #1d4ed8;
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 14px 30px rgba(37, 99, 235, 0.45);
    }

    .btn-hero-secondary {
        background: rgba(255, 255, 255, 0.1);
        color: #ffffff;
        padding: 0.95rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        border: 1px solid rgba(255, 255, 255, 0.3);
        transition: all 0.25s ease;
    }

    .btn-hero-secondary:hover {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
        transform: translateY(-2px);
    }

    /* Barra de estadísticas flotante */
    .stats-bar-wrap {
        margin-top: -3rem;
        position: relative;
        z-index: 10;
        margin-bottom: 2rem;
    }

    .stats-bar {
        background: #ffffff;
        border-radius: 18px;
        padding: 1.75rem 2rem;
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.09);
        border: 1px solid #e2e8f0;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1.5rem;
        text-align: center;
    }

    .stat-item-num {
        display: block;
        font-size: 2.1rem;
        font-weight: 800;
        color: var(--primary-color);
        line-height: 1.1;
        margin-bottom: 0.25rem;
    }

    .stat-item-label {
        font-size: 0.88rem;
        font-weight: 600;
        color: #64748b;
    }

    /* Carrusel Interactivo UMC */
    .carousel-shell {
        position: relative;
        max-width: 1020px;
        margin: 0 auto;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.12);
        background: #0f172a;
    }

    .carousel-track {
        display: flex;
        transition: transform 0.55s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .carousel-slide {
        min-width: 100%;
        height: 440px;
        position: relative;
    }

    .carousel-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .carousel-caption {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 2.5rem 2rem 1.8rem 2rem;
        background: linear-gradient(to top, rgba(15, 23, 42, 0.9) 0%, rgba(15, 23, 42, 0.45) 65%, transparent 100%);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .carousel-caption h3 {
        margin: 0 0 0.25rem 0;
        font-size: 1.45rem;
        color: #ffffff;
    }

    .carousel-caption p {
        margin: 0;
        font-size: 0.92rem;
        color: #cbd5e1;
    }

    .carousel-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, 0.35);
        background: rgba(15, 23, 42, 0.65);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 5;
        transition: all 0.2s ease;
    }

    .carousel-btn:hover {
        background: var(--secondary-color);
        border-color: var(--secondary-color);
        transform: translateY(-50%) scale(1.06);
    }

    .carousel-btn.prev { left: 1.2rem; }
    .carousel-btn.next { right: 1.2rem; }

    .carousel-dots {
        display: flex;
        justify-content: center;
        gap: 0.55rem;
        margin-top: 1.25rem;
    }

    .carousel-dot {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        background: #cbd5e1;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        padding: 0;
    }

    .carousel-dot.active {
        width: 28px;
        background: var(--secondary-color);
    }

    /* Tarjetas de cursos destacados en Home */
    .featured-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.75rem;
        margin-top: 2rem;
    }

    .featured-card {
        background: white;
        border-radius: 16px;
        padding: 1.6rem;
        border: 1px solid #e2e8f0;
        box-shadow: var(--shadow);
        display: flex;
        flex-direction: column;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .featured-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.1);
    }

    .map-legend-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.5rem 1.1rem;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: 999px;
        color: #f8fafc;
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .map-legend-pill:hover {
        background: rgba(255, 255, 255, 0.18);
        color: #ffffff;
        transform: translateY(-2px);
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const track = document.getElementById('umc-carousel-track');
        const slides = document.querySelectorAll('.carousel-slide');
        const dots = document.querySelectorAll('.carousel-dot');
        const prevBtn = document.getElementById('carousel-prev');
        const nextBtn = document.getElementById('carousel-next');
        const shell = document.getElementById('umc-carousel-shell');

        if (!track || slides.length === 0) return;

        let currentSlide = 0;
        let autoTimer = null;

        function goToSlide(index) {
            currentSlide = (index + slides.length) % slides.length;
            track.style.transform = `translateX(-${currentSlide * 100}%)`;
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === currentSlide);
            });
        }

        function startAuto() {
            stopAuto();
            autoTimer = setInterval(() => goToSlide(currentSlide + 1), 5000);
        }

        function stopAuto() {
            if (autoTimer) clearInterval(autoTimer);
        }

        if (prevBtn) prevBtn.addEventListener('click', () => { goToSlide(currentSlide - 1); startAuto(); });
        if (nextBtn) nextBtn.addEventListener('click', () => { goToSlide(currentSlide + 1); startAuto(); });
        dots.forEach((dot, idx) => {
            dot.addEventListener('click', () => { goToSlide(idx); startAuto(); });
        });

        if (shell) {
            shell.addEventListener('mouseenter', stopAuto);
            shell.addEventListener('mouseleave', startAuto);
        }

        startAuto();
    });
</script>
@endpush

@section('content')

<!-- Sección Hero (Presentación inicial) -->
<section class="home-hero">
    <div class="container home-hero-content">
        <span class="hero-pill">Universidad Marítima del Caribe | Sistema UniSpace</span>
        <h1>Reserva tu espacio académico, potencia tu futuro profesional.</h1>
        <p>Consulta la disponibilidad de aulas, laboratorios y salas multimedia en tiempo real. Conoce a nuestros docentes especialistas y asegura tu butaca en segundos.</p>
        <div class="hero-cta-group">
            <a href="{{ route('cursos') }}" class="btn-hero-primary">Ver Cursos y Talleres</a>
            <a href="{{ route('espacios') }}" class="btn-hero-secondary">Explorar Espacios</a>
        </div>
    </div>
</section>

<!-- Barra de métricas reales de la base de datos -->
@if(isset($stats))
<div class="container stats-bar-wrap">
    <div class="stats-bar">
        <div>
            <span class="stat-item-num">{{ $stats['salones'] }}</span>
            <span class="stat-item-label">Aulas y Laboratorios</span>
        </div>
        <div>
            <span class="stat-item-num">{{ $stats['cursos'] }}</span>
            <span class="stat-item-label">Cursos y Talleres Activos</span>
        </div>
        <div>
            <span class="stat-item-num">{{ $stats['profesores'] }}</span>
            <span class="stat-item-label">Docentes Especialistas</span>
        </div>
        <div>
            <span class="stat-item-num">{{ $stats['butacas'] }}</span>
            <span class="stat-item-label">Butacas Habilitadas</span>
        </div>
    </div>
</div>
@endif

<!-- Sección de Cursos Destacados -->
@if(isset($cursosDestacados) && $cursosDestacados->count() > 0)
<section style="padding: 3rem 0 4rem 0;">
    <div class="container">
        <h2 class="section-title" style="color: var(--primary-color);">Oferta Académica Destacada</h2>
        <p class="section-subtitle" style="margin-bottom: 2rem;">Inscríbete en nuestros cursos y talleres impartidos por profesores especialistas de la UMC.</p>

        <div class="featured-grid">
            @foreach($cursosDestacados as $curso)
                <article class="featured-card" style="padding: 0; overflow: hidden;">
                    <div style="height: 165px; position: relative; overflow: hidden; background: #0f172a;">
                        <img src="{{ asset($curso->foto ?: 'images/carousel/edificio_nautica.jpg') }}" alt="{{ $curso->nombre }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                        <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(15,23,42,0.75) 0%, transparent 65%); display: flex; justify-content: space-between; align-items: flex-start; padding: 0.9rem 1.1rem;">
                            <span class="badge badge-{{ $curso->tipo }}" style="margin-bottom: 0; background: #2563eb; color: #fff;">{{ $curso->tipoBadge() }}</span>
                            <span style="font-size: 0.78rem; color: #fff; background: rgba(15,23,42,0.7); padding: 0.2rem 0.6rem; border-radius: 6px; font-weight: 600;">Salón {{ $curso->salon->codigo }}</span>
                        </div>
                    </div>
                    <div style="padding: 1.4rem; display: flex; flex-direction: column; flex: 1;">
                    <h3 style="margin-bottom: 0.5rem; color: #0f172a;">{{ $curso->nombre }}</h3>
                    <p style="color: #475569; font-size: 0.92rem; flex: 1; margin-bottom: 1.2rem;">{{ $curso->descripcion }}</p>
                    
                    @if($curso->profesor)
                        <a href="{{ route('profesores', ['profesor' => $curso->profesor->nombreCompleto()]) }}" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 0.85rem; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0; margin-bottom: 1rem; text-decoration: none; color: inherit;">
                            @if($curso->profesor->fotoPerfilUrl())
                                <img src="{{ $curso->profesor->fotoPerfilUrl() }}" alt="{{ $curso->profesor->nombreCompleto() }}" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover;">
                            @else
                                <div style="width: 38px; height: 38px; border-radius: 50%; background: var(--primary-color); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem;">
                                    {{ strtoupper(substr($curso->profesor->nombre, 0, 1) . substr($curso->profesor->apellido, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <strong style="display: block; font-size: 0.88rem; color: #0f172a;">Prof. {{ $curso->profesor->nombreCompleto() }}</strong>
                                <span style="font-size: 0.76rem; color: var(--secondary-color); font-weight: 600;">Ver perfil docente</span>
                            </div>
                        </a>
                    @endif

                    <a href="{{ route('cursos', ['curso' => $curso->nombre]) }}" class="btn btn-outline btn-block" style="padding: 0.6rem;">Ver horarios y reservar</a>
                    </div>
                </article>
            @endforeach
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="{{ route('cursos') }}" class="btn btn-primary">Ver todos los Cursos y Talleres</a>
        </div>
    </div>
</section>
@endif

<!-- Sección Carrusel de Imágenes -->
<section id="carrusel" style="padding: 4rem 0; background-color: #ffffff; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <h2 class="section-title" style="text-align: center; margin-bottom: 0.5rem; color: var(--primary-color);">Nuestras Instalaciones</h2>
        <p class="section-subtitle" style="margin-bottom: 2rem;">Recorre los espacios académicos, científicos y recreativos de la Universidad Marítima del Caribe.</p>
        
        <div class="carousel-shell" id="umc-carousel-shell">
            <button type="button" class="carousel-btn prev" id="carousel-prev" aria-label="Anterior">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </button>
            <button type="button" class="carousel-btn next" id="carousel-next" aria-label="Siguiente">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>

            <div class="carousel-track" id="umc-carousel-track">
                <div class="carousel-slide">
                    <img src="{{ asset('images/carousel/lab_tecnologia.jpg') }}" alt="Sala de Multimedia">
                    <div class="carousel-caption">
                        <div>
                            <h3>Salas de Tecnología y Multimedia</h3>
                            <p>Equipadas con estaciones de cómputo y conectividad para prácticas de programación y simulación.</p>
                        </div>
                        <a href="{{ route('espacios', ['tipo' => 'multimedia']) }}" class="btn btn-primary btn-sm">Ver Salas Multimedia</a>
                    </div>
                </div>
                <div class="carousel-slide">
                    <img src="{{ asset('images/carousel/entrada_principal.jpg') }}" alt="Entrada Principal">
                    <div class="carousel-caption">
                        <div>
                            <h3>Entrada Principal UMC</h3>
                            <p>Sede principal en Catia la Mar, Estado La Guaira, formando profesionales integrales.</p>
                        </div>
                        <a href="{{ route('espacios') }}" class="btn btn-primary btn-sm">Ver Espacios</a>
                    </div>
                </div>
                <div class="carousel-slide">
                    <img src="{{ asset('images/carousel/edificio_nautica.jpg') }}" alt="Escuela Náutica de Venezuela">
                    <div class="carousel-caption">
                        <div>
                            <h3>Escuela Náutica de Venezuela</h3>
                            <p>Tradición y excelencia académica en formación marítima e ingeniería naval.</p>
                        </div>
                        <a href="{{ route('cursos') }}" class="btn btn-primary btn-sm">Explorar Cursos</a>
                    </div>
                </div>
                <div class="carousel-slide">
                    <img src="{{ asset('images/carousel/areas_verdes.jpg') }}" alt="Áreas Verdes y Recreación">
                    <div class="carousel-caption">
                        <div>
                            <h3>Áreas Verdes y Campus Universitario</h3>
                            <p>Espacios abiertos pensados para el estudio colaborativo y el bienestar estudiantil.</p>
                        </div>
                        <a href="{{ route('profesores') }}" class="btn btn-primary btn-sm">Conocer Docentes</a>
                    </div>
                </div>
                <div class="carousel-slide">
                    <img src="{{ asset('images/carousel/laboratorio.jpg') }}" alt="Laboratorio de Ciencias">
                    <div class="carousel-caption">
                        <div>
                            <h3>Laboratorios de Ciencias y Física</h3>
                            <p>Ambientes especializados para prácticas experimentales de química, física y termodinámica.</p>
                        </div>
                        <a href="{{ route('espacios', ['tipo' => 'laboratorio']) }}" class="btn btn-primary btn-sm">Ver Laboratorios</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="carousel-dots">
            <button type="button" class="carousel-dot active" aria-label="Diapositiva 1"></button>
            <button type="button" class="carousel-dot" aria-label="Diapositiva 2"></button>
            <button type="button" class="carousel-dot" aria-label="Diapositiva 3"></button>
            <button type="button" class="carousel-dot" aria-label="Diapositiva 4"></button>
            <button type="button" class="carousel-dot" aria-label="Diapositiva 5"></button>
        </div>
    </div>
</section>

<!-- Sección Mapa del Campus -->
<section id="mapa" class="mapa-section" style="padding: 4.5rem 0;">
    <div class="container">
        <h2 class="section-title" style="text-align: center; margin-bottom: 0.6rem; color: #ffffff;">Mapa del Campus</h2>
        <p style="text-align: center; color: #cbd5e1; margin-bottom: 1.8rem;">Selecciona un área académica para consultar sus cursos o explora el plano general de la institución.</p>
        
        <!-- Leyenda interactiva -->
        <div style="display: flex; justify-content: center; gap: 1rem; margin-bottom: 2.2rem; flex-wrap: wrap;">
            <a href="{{ route('cursos', ['curso' => 'Tecnología']) }}" class="map-legend-pill">
                <span style="width:14px;height:14px;background:#3b82f6;border-radius:4px;display:inline-block;"></span> Tecnología
            </a>
            <a href="{{ route('cursos', ['curso' => 'Idiomas']) }}" class="map-legend-pill">
                <span style="width:14px;height:14px;background:#ef4444;border-radius:4px;display:inline-block;"></span> Idiomas
            </a>
            <a href="{{ route('cursos', ['curso' => 'Química']) }}" class="map-legend-pill">
                <span style="width:14px;height:14px;background:#22c55e;border-radius:4px;display:inline-block;"></span> Ciencias
            </a>
            <a href="{{ route('cursos', ['curso' => 'Cálculo']) }}" class="map-legend-pill">
                <span style="width:14px;height:14px;background:#eab308;border-radius:4px;display:inline-block;"></span> Numéricos
            </a>
        </div>

        <div style="text-align: center; width: 100%;">
            <div style="background: #ffffff; border-radius: 16px; padding: 0.75rem; box-shadow: 0 16px 36px rgba(0,0,0,0.28); display: inline-block; max-width: 100%;">
                <img src="{{ asset('images/Mapa.png.jpeg') }}" alt="Plano General de la Universidad" style="max-width: 100%; max-height: 75vh; width: auto; height: auto; border-radius: 10px; display: block; margin: 0 auto;">
            </div>
        </div>
    </div>
</section>

<!-- Sección Google Maps Interactivo -->
<section id="ubicacion" style="padding: 4.5rem 0; background-color: var(--bg-color);">
    <div class="container">
        <h2 class="section-title" style="text-align: center; margin-bottom: 0.5rem; color: var(--primary-color);">Nuestra Ubicación</h2>
        <p style="text-align: center; margin-bottom: 2rem; color: #475569;">Av. El Ejército, Catia la Mar, Edo. La Guaira, Venezuela.</p>
        
        <div style="width: 100%; max-width: 1020px; margin: 0 auto; border-radius: 16px; overflow: hidden; box-shadow: var(--shadow); height: 450px; border: 1px solid #e2e8f0;">
            <iframe 
                width="100%" 
                height="100%" 
                frameborder="0" 
                scrolling="no" 
                marginheight="0" 
                marginwidth="0" 
                src="https://maps.google.com/maps?q=Universidad+Maritima+del+Caribe,+Catia+La+Mar,+Venezuela&t=&z=15&ie=UTF8&iwloc=&output=embed"
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</section>

@endsection
