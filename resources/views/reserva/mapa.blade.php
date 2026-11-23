@extends('layouts.public')

@section('title', 'Seleccionar Butaca')

@section('content')
@php
    $totalButacas = $butacas->count();
    $ocupadasCount = count($butacasOcupadas);
    $pendientesCount = isset($butacasPendientes) ? count($butacasPendientes) : 0;
    $disponiblesCount = max(0, $totalButacas - $ocupadasCount);
    $profesor = $funcion->curso->profesor;
    $esEvento = $funcion->curso->esEvento();
@endphp
<section class="reserva-section">
    <div class="container">
        
        <div class="reserva-header">
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1rem;">
                @if($esEvento)
                    <a href="{{ route('auditorio') }}" class="btn btn-outline btn-sm">Volver a Auditorio y Eventos</a>
                @else
                    <a href="{{ route('cursos') }}" class="btn btn-outline btn-sm">Volver a Cursos y Talleres</a>
                @endif
                <a href="{{ route('dashboard') }}" class="btn btn-outline btn-sm">Ir a mi Dashboard</a>
            </div>

            <div class="reserva-title" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
                <div>
                    <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.4rem;">
                        <span class="badge badge-{{ $funcion->curso->tipo }}" style="margin-bottom: 0;">{{ $funcion->curso->tipoBadge() }}</span>
                        @if($esEvento)
                            <span style="background: #0f172a; color: #fde047; font-weight: 800; font-size: 0.82rem; padding: 0.2rem 0.75rem; border-radius: 999px;">
                                Costo: ${{ number_format($funcion->curso->precio, 2) }} USD (Pago Móvil)
                            </span>
                        @endif
                    </div>
                    <h2 style="margin-bottom: 0.35rem; color: #0f172a;">{{ $funcion->curso->nombre }}</h2>
                    <p style="color: #475569; font-weight: 500; margin: 0;">
                        <strong>Recinto:</strong> {{ $salon->nombre }} ({{ $salon->codigo }}) |
                        <strong>Horario:</strong> {{ $funcion->diaDisplay() }}, {{ $funcion->horario() }}
                    </p>
                </div>

                @if($profesor)
                    <a href="{{ route('profesores', ['profesor' => $profesor->nombreCompleto()]) }}" style="display: flex; align-items: center; gap: 0.8rem; background: #f8fafc; padding: 0.65rem 1rem; border-radius: 12px; border: 1px solid #e2e8f0; text-decoration: none; color: inherit;">
                        @if($profesor->fotoPerfilUrl())
                            <img src="{{ $profesor->fotoPerfilUrl() }}" alt="{{ $profesor->nombreCompleto() }}" style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 2px solid var(--secondary-color);">
                        @else
                            <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--primary-color); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                                {{ strtoupper(substr($profesor->nombre, 0, 1) . substr($profesor->apellido, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <span style="font-size: 0.75rem; color: #64748b; display: block; font-weight: 600;">{{ $esEvento ? 'Coordinador del Evento' : 'Docente Encargado' }}</span>
                            <strong style="color: #0f172a; font-size: 0.95rem;">Prof. {{ $profesor->nombreCompleto() }}</strong>
                        </div>
                    </a>
                @endif
            </div>

            <!-- Selector de horario si el curso tiene varias funciones -->
            @if($funcion->curso->funciones && $funcion->curso->funciones->count() > 1)
                <div style="margin-top: 1rem; background: #ffffff; padding: 1rem 1.25rem; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                    <span style="font-size: 0.88rem; font-weight: 700; color: #334155;">Cambiar horario:</span>
                    @foreach($funcion->curso->funciones as $f)
                        <a href="{{ route('reserva.mapa', $f->id) }}"
                           style="padding: 0.4rem 0.9rem; border-radius: 8px; font-size: 0.85rem; font-weight: 600; text-decoration: none; border: 1px solid {{ $f->id === $funcion->id ? 'var(--secondary-color)' : '#cbd5e1' }}; background: {{ $f->id === $funcion->id ? 'var(--secondary-color)' : '#f8fafc' }}; color: {{ $f->id === $funcion->id ? '#ffffff' : '#334155' }};">
                            {{ $f->diaDisplay() }} ({{ $f->horario() }})
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger" style="margin-top: 1rem;">
                {{ $errors->first() }}
            </div>
        @endif

        @if(!$salon->estaDisponible())
            <div class="alert alert-danger" style="margin-top: 1rem;">
                Este recinto ({{ $salon->nombre }}) se encuentra cerrado o en mantenimiento por disposición del Administrador.
            </div>
        @endif

        @if($yaReservado)
            <div class="alert alert-warning" style="margin-top: 1rem; background: #fff3cd; color: #856404; border: 1px solid #ffeeba;">
                Ya tienes una reserva o solicitud en verificación para este horario. Puedes consultarla en tu Dashboard.
            </div>
        @endif

        <div class="mapa-wrapper">
            <div style="display: flex; justify-content: center; gap: 1.5rem; flex-wrap: wrap; margin-bottom: 1.8rem; color: #cbd5e1; font-size: 0.9rem;">
                <span>Capacidad total: <strong style="color: #fff;">{{ $totalButacas }}</strong></span>
                <span>Disponibles: <strong style="color: #4ade80;">{{ $disponiblesCount }}</strong></span>
                @if($esEvento)
                    <span>En verificación de pago: <strong style="color: #facc15;">{{ $pendientesCount }}</strong></span>
                @endif
                <span>Ocupadas: <strong style="color: #f87171;">{{ $ocupadasCount - $pendientesCount }}</strong></span>
            </div>

            <div class="pizarron" style="{{ $esEvento ? 'width: 75%; background: linear-gradient(90deg, #1e293b, #334155, #1e293b); color: #fde047; border: 1px solid rgba(250,204,21,0.35); height: 48px;' : '' }}">
                <span>{{ $esEvento ? 'ESCENARIO PRINCIPAL - AUDITORIO UMC' : 'PIZARRÓN / ESCRITORIO DEL PROFESOR' }}</span>
            </div>

            <!-- El estilo CSS Grid se inyecta dinámicamente según las dimensiones del salón -->
            <div class="butacas-grid" style="grid-template-columns: repeat({{ $salon->columnas }}, 1fr); grid-template-rows: repeat({{ $salon->filas }}, 1fr); max-width: {{ $salon->columnas > 8 ? '980px' : '800px' }}; gap: {{ $salon->columnas > 8 ? '10px' : '15px' }};">
                
                @foreach($butacas as $butaca)
                    @php
                        $isPendiente = isset($butacasPendientes) && in_array($butaca->id, $butacasPendientes);
                        $isOcupada = in_array($butaca->id, $butacasOcupadas);
                        $isDisabled = $isOcupada || $yaReservado || !$salon->estaDisponible(); 
                    @endphp

                    <div class="butaca {{ $isDisabled ? 'butaca-ocupada' : 'butaca-disponible' }}" 
                         style="{{ $isPendiente ? 'background: #d97706; opacity: 0.9;' : '' }}"
                         data-id="{{ $butaca->id }}" 
                         data-etiqueta="{{ $butaca->etiqueta }}"
                         title="Asiento {{ $butaca->etiqueta }} {{ $isPendiente ? '(En verificación de pago)' : ($isOcupada ? '(Ocupado)' : '(Disponible)') }}">
                        {{ $butaca->etiqueta }}
                    </div>
                @endforeach

            </div>
            
            <div class="leyenda">
                <div class="leyenda-item">
                    <div class="butaca butaca-disponible" style="width: 30px; height: 30px; pointer-events: none;"></div>
                    <span>Disponible</span>
                </div>
                <div class="leyenda-item">
                    <div class="butaca butaca-seleccionada" style="width: 30px; height: 30px; pointer-events: none;"></div>
                    <span>Seleccionada</span>
                </div>
                @if($esEvento)
                <div class="leyenda-item">
                    <div class="butaca" style="width: 30px; height: 30px; background: #d97706; pointer-events: none;"></div>
                    <span>En Verificación</span>
                </div>
                @endif
                <div class="leyenda-item">
                    <div class="butaca butaca-ocupada" style="width: 30px; height: 30px; pointer-events: none;"></div>
                    <span>Ocupada</span>
                </div>
            </div>
        </div>

        @if($esEvento)
            <!-- PASARELA DE PAGO MÓVIL Y SOLICITUD PARA EL AUDITORIO -->
            <div class="reserva-footer" style="flex-direction: column; align-items: stretch; gap: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem;">
                    <div class="seleccion-info">
                        <span>Butaca de Auditorio seleccionada: </span>
                        <strong id="asiento-label">Ninguno</strong>
                    </div>
                    <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e3a8a; padding: 0.5rem 1rem; border-radius: 10px; font-weight: 700;">
                        Monto a cancelar: ${{ number_format($funcion->curso->precio, 2) }} USD
                    </div>
                </div>

                <form action="{{ route('reserva.store', $funcion->id) }}" method="POST" id="form-reserva">
                    @csrf
                    <input type="hidden" name="butaca_id" id="butaca_id" value="" required>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; align-items: start;">
                        <!-- Columna 1: Datos de Pago Móvil y Efectivo UMC -->
                        <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 12px; padding: 1.25rem;">
                            <h4 style="margin: 0 0 0.5rem 0; color: #0f172a; font-size: 1.05rem;">1. Opciones de Pago UMC</h4>
                            <p style="font-size: 0.84rem; color: #475569; margin-bottom: 0.85rem;">
                                Puedes cancelar por <strong>Pago Móvil</strong> a la cuenta oficial de la UMC o en <strong>Efectivo</strong> en taquilla:
                            </p>
                            <div style="font-size: 0.88rem; display: flex; flex-direction: column; gap: 0.4rem; background: #ffffff; padding: 0.85rem 1rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <div><strong>Banco:</strong> 0102 - Banco de Venezuela</div>
                                <div><strong>RIF:</strong> G-20003807-1</div>
                                <div><strong>Teléfono Pago Móvil:</strong> 0212-3500100</div>
                                <div><strong>Monto:</strong> ${{ number_format($funcion->curso->precio, 2) }} USD (Tasa BCV)</div>
                            </div>

                            <div style="margin-top: 1rem; padding: 0.85rem 1rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px;">
                                <strong style="display: block; font-size: 0.85rem; color: #166534; margin-bottom: 0.25rem;">
                                    Pago en Efectivo y Atención Presencial
                                </strong>
                                <p style="font-size: 0.8rem; color: #14532d; margin: 0; line-height: 1.45;">
                                    Si pagas en <strong>efectivo</strong>, o necesitas terminar de confirmar tu pago o solventar algún inconveniente, debes acudir de manera <strong>presencial</strong> a la Taquilla Administrativa de la UMC.
                                </p>
                            </div>
                        </div>

                        <!-- Columna 2: Formulario de Pasarela -->
                        <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 1.25rem;">
                            <h4 style="margin: 0 0 0.5rem 0; color: #0f172a; font-size: 1.05rem;">2. Confirmar Método de Pago</h4>
                            <p style="font-size: 0.84rem; color: #475569; margin-bottom: 1rem;">
                                Tu butaca quedará apartada en estado pendiente hasta que el Administrador confirme tu pago (por referencia o presencial en taquilla).
                            </p>

                            <div class="form-group" style="margin-bottom: 0.9rem;">
                                <label for="metodo_pago" style="font-size: 0.85rem;">Método de Pago</label>
                                <select name="metodo_pago" id="metodo_pago" required style="width: 100%; padding: 0.7rem 0.9rem; border: 1px solid #cbd5e1; border-radius: 8px; font-family: var(--font-main); background: #f8fafc;">
                                    <option value="pago_movil">Pago Móvil (0212-3500100)</option>
                                    <option value="efectivo">Efectivo (Taquilla Presencial UMC)</option>
                                </select>
                            </div>

                            <div id="campos-pago-movil">
                                <div class="form-group" style="margin-bottom: 0.9rem;">
                                    <label for="banco_pago" style="font-size: 0.85rem;">Banco Emisor del Pago Móvil</label>
                                    <select name="banco_pago" id="banco_pago" required style="width: 100%; padding: 0.7rem 0.9rem; border: 1px solid #cbd5e1; border-radius: 8px; font-family: var(--font-main); background: #f8fafc;">
                                        <option value="">Selecciona tu banco...</option>
                                        <option value="0102 - Banco de Venezuela">0102 - Banco de Venezuela</option>
                                        <option value="0134 - Banesco">0134 - Banesco</option>
                                        <option value="0105 - Banco Mercantil">0105 - Banco Mercantil</option>
                                        <option value="0108 - BBVA Provincial">0108 - BBVA Provincial</option>
                                        <option value="0191 - BNC">0191 - Banco Nacional de Crédito (BNC)</option>
                                        <option value="0172 - Bancamiga">0172 - Bancamiga</option>
                                        <option value="0114 - Bancaribe">0114 - Bancaribe</option>
                                    </select>
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem; margin-bottom: 1.1rem;">
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label for="telefono_pago" style="font-size: 0.85rem;">Teléfono Emisor</label>
                                        <input type="text" name="telefono_pago" id="telefono_pago" value="{{ Auth::user()->telefono }}" required placeholder="Ej: 04121234567" maxlength="15">
                                    </div>
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label for="referencia_pago" style="font-size: 0.85rem;">Número de Referencia</label>
                                        <input type="text" name="referencia_pago" id="referencia_pago" required placeholder="Últimos 6 u 8 dígitos" maxlength="30">
                                    </div>
                                </div>
                            </div>

                            <div id="aviso-efectivo" style="display: none; background: #fffbeb; border: 1px solid #fde68a; color: #92400e; border-radius: 8px; padding: 0.85rem 1rem; font-size: 0.83rem; margin-bottom: 1.1rem; line-height: 1.45;">
                                <strong>Reserva en Efectivo:</strong> Al enviar la solicitud, tu butaca quedará apartada. Preséntate en la Taquilla Administrativa UMC para cancelar <strong>${{ number_format($funcion->curso->precio, 2) }} USD</strong> en efectivo y terminar de confirmar tu pago.
                            </div>

                            <button type="submit" class="btn btn-primary btn-block" id="btn-reservar" disabled style="padding: 0.85rem;">
                                Enviar Solicitud de Reserva
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @else
            <div class="reserva-footer">
                <div class="seleccion-info">
                    <span>Asiento seleccionado: </span>
                    <strong id="asiento-label">Ninguno</strong>
                </div>

                <form action="{{ route('reserva.store', $funcion->id) }}" method="POST" id="form-reserva">
                    @csrf
                    <input type="hidden" name="butaca_id" id="butaca_id" value="" required>
                    <button type="submit" class="btn btn-primary btn-large" id="btn-reservar" disabled>Confirmar Reserva</button>
                </form>
            </div>
        @endif
    </div>
</section>

@if($esEvento)
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const metodoSelect = document.getElementById('metodo_pago');
        const camposPm = document.getElementById('campos-pago-movil');
        const avisoEfectivo = document.getElementById('aviso-efectivo');
        const banco = document.getElementById('banco_pago');
        const telefono = document.getElementById('telefono_pago');
        const referencia = document.getElementById('referencia_pago');

        function toggleMetodoPago() {
            const esEfectivo = metodoSelect && metodoSelect.value === 'efectivo';
            if (camposPm) camposPm.style.display = esEfectivo ? 'none' : 'block';
            if (avisoEfectivo) avisoEfectivo.style.display = esEfectivo ? 'block' : 'none';
            if (banco) banco.required = !esEfectivo;
            if (telefono) telefono.required = !esEfectivo;
            if (referencia) referencia.required = !esEfectivo;
        }

        if (metodoSelect) {
            metodoSelect.addEventListener('change', toggleMetodoPago);
            toggleMetodoPago();
        }
    });
</script>
@endpush
@endif
@endsection
