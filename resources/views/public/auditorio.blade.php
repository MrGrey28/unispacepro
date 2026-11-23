@extends('layouts.public')

@section('title', 'Auditorio y Eventos')

@push('scripts')
<style>
    .auditorio-hero {
        position: relative;
        padding: 4.5rem 0;
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.92) 0%, rgba(30, 58, 95, 0.88) 100%), url("{{ asset($auditorio && $auditorio->foto ? $auditorio->foto : 'images/salones/auditorio.jpg') }}") center/cover no-repeat;
        color: #ffffff;
    }

    .auditorio-hero-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 2.5rem;
        align-items: center;
    }

    .auditorio-badge {
        display: inline-block;
        padding: 0.35rem 0.95rem;
        border-radius: 999px;
        background: rgba(234, 179, 8, 0.2);
        border: 1px solid rgba(250, 204, 21, 0.45);
        color: #fde047;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 1rem;
    }

    .pago-movil-card {
        background: rgba(255, 255, 255, 0.97);
        color: #0f172a;
        border-radius: 16px;
        padding: 1.75rem;
        box-shadow: 0 18px 38px rgba(0, 0, 0, 0.28);
        border: 1px solid #e2e8f0;
    }

    .pago-movil-card h3 {
        margin: 0 0 0.4rem 0;
        color: #0f172a;
        font-size: 1.2rem;
    }

    .pm-data-list {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 1rem 1.15rem;
        margin: 1rem 0;
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
        font-size: 0.9rem;
    }

    .pm-data-row {
        display: flex;
        justify-content: space-between;
        border-bottom: 1px dashed #e2e8f0;
        padding-bottom: 0.35rem;
    }

    .pm-data-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .btn-whatsapp-taquilla {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        padding: 0.75rem 1rem;
        border-radius: 10px;
        background: #16a34a;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.9rem;
        text-decoration: none;
        transition: background-color 0.2s, transform 0.2s;
    }

    .btn-whatsapp-taquilla:hover {
        background: #15803d;
        color: #ffffff;
        transform: translateY(-2px);
    }

    .eventos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }

    .evento-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.06);
        display: flex;
        flex-direction: column;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .evento-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 32px rgba(15, 23, 42, 0.12);
    }

    .evento-cover {
        position: relative;
        height: 195px;
        background: #0f172a;
        overflow: hidden;
    }

    .evento-cover img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .evento-card:hover .evento-cover img {
        transform: scale(1.06);
    }

    .evento-price-tag {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: #0f172a;
        color: #fde047;
        border: 1px solid rgba(250, 204, 21, 0.5);
        padding: 0.35rem 0.85rem;
        border-radius: 999px;
        font-weight: 800;
        font-size: 0.95rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }

    .evento-body {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .steps-strip {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2.5rem;
    }

    .step-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.25rem;
        box-shadow: var(--shadow);
    }

    .step-num {
        display: inline-flex;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: var(--secondary-color);
        color: white;
        font-weight: 800;
        font-size: 0.85rem;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.5rem;
    }

    @media (max-width: 900px) {
        .auditorio-hero-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<!-- HERO DEL AUDITORIO PRINCIPAL -->
<section class="auditorio-hero">
    <div class="container auditorio-hero-grid">
        <div>
            <span class="auditorio-badge">Recinto Especial UMC • Capacidad Ampliada</span>
            <h1 style="font-size: clamp(2rem, 4vw, 2.85rem); color: #ffffff; margin-bottom: 1rem; line-height: 1.15;">
                {{ $auditorio ? $auditorio->nombre : 'Auditorio Principal UMC' }}
            </h1>
            <p style="color: #cbd5e1; font-size: 1.05rem; line-height: 1.65; margin-bottom: 1.5rem;">
                Espacio exclusivo para Actos Solemnes de Graduación, Congresos Internacionales, Conferencias Magistrales y Eventos Especiales de la Universidad Marítima del Caribe.
            </p>

            @if($auditorio)
                <div style="display: flex; flex-wrap: wrap; gap: 0.85rem; margin-bottom: 1.2rem;">
                    <span style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); padding: 0.45rem 0.95rem; border-radius: 8px; font-size: 0.88rem; font-weight: 600;">
                        Código: {{ $auditorio->codigo }}
                    </span>
                    <span style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); padding: 0.45rem 0.95rem; border-radius: 8px; font-size: 0.88rem; font-weight: 600;">
                        Capacidad: {{ $auditorio->capacidad }} Butacas ({{ $auditorio->filas }}x{{ $auditorio->columnas }})
                    </span>
                    <span style="background: {{ $auditorio->estaDisponible() ? 'rgba(22,163,74,0.28)' : 'rgba(220,38,38,0.35)' }}; border: 1px solid {{ $auditorio->estaDisponible() ? '#4ade80' : '#f87171' }}; padding: 0.45rem 0.95rem; border-radius: 8px; font-size: 0.88rem; font-weight: 700;">
                        Estado: {{ $auditorio->estaDisponible() ? 'Abierto para Reservas' : 'Cerrado / En Mantenimiento' }}
                    </span>
                </div>
                <p style="font-size: 0.88rem; color: #94a3b8; margin: 0;">
                    <strong>Equipamiento:</strong> {{ $auditorio->equipos }}
                </p>
            @endif
        </div>

        <!-- Tarjeta de Datos de Pago Móvil y Efectivo Presencial -->
        <div class="pago-movil-card">
            <h3>Taquilla de Pagos UMC</h3>
            <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 0.5rem;">
                Para reservar una butaca en los eventos del Auditorio, puedes cancelar por <strong>Pago Móvil</strong> o en <strong>Efectivo</strong>:
            </p>

            <div class="pm-data-list">
                <div class="pm-data-row">
                    <span style="color: #64748b;">Banco Receptor:</span>
                    <strong>0102 - Banco de Venezuela</strong>
                </div>
                <div class="pm-data-row">
                    <span style="color: #64748b;">RIF Institucional:</span>
                    <strong>G-20003807-1</strong>
                </div>
                <div class="pm-data-row">
                    <span style="color: #64748b;">Teléfono Pago Móvil:</span>
                    <strong>0212-3500100</strong>
                </div>
                <div class="pm-data-row">
                    <span style="color: #64748b;">Concepto:</span>
                    <strong>Evento Auditorio UMC</strong>
                </div>
            </div>

            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 0.85rem 1rem; margin-bottom: 0.85rem;">
                <strong style="display: block; color: #166534; font-size: 0.86rem; margin-bottom: 0.25rem;">Pago en Efectivo (Taquilla Presencial)</strong>
                <p style="font-size: 0.8rem; color: #14532d; margin: 0; line-height: 1.45;">
                    También aceptamos pagos en efectivo directamente en la <strong>Taquilla Administrativa del campus UMC</strong>.
                </p>
            </div>

            <p style="font-size: 0.8rem; color: #475569; margin: 0; line-height: 1.45;">
                Selecciona tu asiento abajo para reportar tu referencia de <strong>Pago Móvil</strong> o solicitar tu reserva en <strong>Efectivo</strong>. Para terminar de confirmar tu pago o solventar cualquier problema, acude de manera <strong>presencial</strong> a taquilla.
            </p>
        </div>
    </div>
</section>

<!-- SECCIÓN DE EVENTOS Y GRADUACIONES DEL AUDITORIO -->
<section style="padding: 3.5rem 0 5rem 0;">
    <div class="container">
        <h2 class="section-title">Eventos y Graduaciones Programadas</h2>
        <p class="section-subtitle" style="margin-bottom: 2rem;">
            Selecciona tu evento, elige tu butaca en el plano de 96 asientos del Auditorio y registra tu pago por Pago Móvil o en Efectivo.
        </p>

        <!-- Explicación del flujo en 3 pasos -->
        <div class="steps-strip">
            <div class="step-box">
                <span class="step-num">1</span>
                <h4 style="margin-bottom: 0.3rem; font-size: 1rem;">Elige Evento y Butaca</h4>
                <p style="font-size: 0.85rem; color: #64748b; margin: 0;">Ingresa al mapa interactivo de 96 asientos del Auditorio y selecciona tu lugar preferido.</p>
            </div>
            <div class="step-box">
                <span class="step-num">2</span>
                <h4 style="margin-bottom: 0.3rem; font-size: 1rem;">Pago Móvil o Efectivo</h4>
                <p style="font-size: 0.85rem; color: #64748b; margin: 0;">Reporta tu referencia de Pago Móvil en la pasarela o elige pago en Efectivo y confirma presencialmente en taquilla.</p>
            </div>
            <div class="step-box">
                <span class="step-num">3</span>
                <h4 style="margin-bottom: 0.3rem; font-size: 1rem;">Verificación, Correo QR y Agenda</h4>
                <p style="font-size: 0.85rem; color: #64748b; margin: 0;">Al confirmar el Administrador tu pago (o en taquilla presencial), recibes tu ticket con QR y el botón para agendar en Google Calendar.</p>
            </div>
        </div>

        @if($auditorio && !$auditorio->estaDisponible())
            <div class="alert alert-danger" style="margin-bottom: 2rem;">
                <strong>Aviso Importante:</strong> El Auditorio Principal se encuentra actualmente cerrado o en mantenimiento por disposición del Administrador. Las reservas están temporalmente pausadas.
            </div>
        @endif

        <div class="eventos-grid">
            @forelse($eventos as $evento)
                @php
                    $funcion = $evento->funciones->first();
                    $ocupados = $funcion ? $funcion->tickets->whereIn('estado', ['activo', 'pendiente'])->count() : 0;
                    $cupoMax = $funcion ? $funcion->cupo_maximo : ($auditorio->capacidad ?? 96);
                    $disponibles = max(0, $cupoMax - $ocupados);
                @endphp
                <article class="evento-card">
                    <div class="evento-cover">
                        <img src="{{ asset($evento->foto ?: 'images/salones/auditorio.jpg') }}" alt="{{ $evento->nombre }}">
                        <span class="evento-price-tag">${{ number_format($evento->precio, 2) }} USD</span>
                    </div>

                    <div class="evento-body">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <span class="badge" style="background: #fef3c7; color: #92400e; margin-bottom: 0;">{{ $evento->tipoBadge() }}</span>
                            <span style="font-size: 0.78rem; font-weight: 700; color: #64748b;">Código: {{ $evento->codigo }}</span>
                        </div>

                        <h3 style="margin-bottom: 0.5rem; color: #0f172a; font-size: 1.22rem; overflow-wrap: anywhere; word-break: break-word;">{{ $evento->nombre }}</h3>
                        <div style="font-size: 0.9rem; color: #475569; flex: 1; margin-bottom: 1.1rem; white-space: pre-line; overflow-wrap: anywhere; word-wrap: break-word; word-break: break-word; line-height: 1.6;">{{ $evento->descripcion }}</div>

                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.8rem 1rem; margin-bottom: 1.1rem; font-size: 0.85rem; color: #334155; display: flex; flex-direction: column; gap: 0.3rem;">
                            <div><strong>Fecha del Evento:</strong> {{ $evento->fecha_inicio->format('d/m/Y') }}</div>
                            @if($funcion)
                                <div><strong>Horario:</strong> {{ $funcion->diaDisplay() }}, {{ $funcion->horario() }}</div>
                            @endif
                            <div><strong>Coordinador Responsable:</strong> Prof. {{ $evento->profesor ? $evento->profesor->nombreCompleto() : 'Comité UMC' }}</div>
                            <div>
                                <strong>Butacas Disponibles:</strong>
                                <span style="color: {{ $disponibles > 0 ? 'var(--success)' : 'var(--danger)' }}; font-weight: 700;">
                                    {{ $disponibles }} de {{ $cupoMax }} asientos libres
                                </span>
                            </div>
                        </div>

                        <div style="margin-top: auto; display: flex; flex-direction: column; gap: 0.55rem;">
                            @if($auditorio && !$auditorio->estaDisponible())
                                <button class="btn btn-outline btn-block" disabled style="opacity: 0.6; cursor: not-allowed;">Auditorio Cerrado</button>
                            @else
                                @auth
                                    @if($funcion)
                                        <a href="{{ route('reserva.mapa', $funcion->id) }}" class="btn btn-primary btn-block">
                                            Seleccionar Butaca y Pagar (${{ number_format($evento->precio, 2) }})
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-outline btn-block">
                                        Inicia sesión para adquirir butaca
                                    </a>
                                @endauth
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="empty-state" style="grid-column: 1 / -1;">
                    <p>No hay eventos programados en el Auditorio en este momento.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection

