@extends('layouts.public')

@section('title', 'Panel de Administracion')

@section('content')
<section class="dashboard-section" style="padding: 2.5rem 0 4rem 0; background-color: #f8fafc;">
    <div class="container">

        <!-- CABECERA DEL PANEL DE ADMINISTRADOR -->
        <div style="background: linear-gradient(135deg, #0f172a 0%, #1d3557 100%); color: #ffffff; border-radius: 14px; padding: 1.75rem 2rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; box-shadow: 0 10px 25px rgba(15,23,42,0.12);">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 54px; height: 54px; border-radius: 12px; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.3rem; border: 1px solid rgba(255,255,255,0.25);">
                    ADM
                </div>
                <div>
                    <span style="display: inline-block; background: #eab308; color: #0f172a; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; padding: 0.2rem 0.65rem; border-radius: 50px; margin-bottom: 0.3rem;">Control Maestro en Tiempo Real</span>
                    <h2 style="margin: 0; font-size: 1.55rem; color: #ffffff;">Panel de Administracion UnispacePro</h2>
                    <p style="margin: 0.2rem 0 0 0; font-size: 0.88rem; color: #cbd5e1;">Edita cursos, talleres, eventos, espacios, estados de salones, profesores y pagos del Auditorio sin tocar codigo.</p>
                </div>
            </div>
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <a href="{{ route('auditorio') }}" class="btn btn-outline btn-sm" style="color: #ffffff; border-color: rgba(255,255,255,0.4);">Ver Auditorio Publico</a>
                <a href="{{ route('espacios') }}" class="btn btn-outline btn-sm" style="color: #ffffff; border-color: rgba(255,255,255,0.4);">Ver Espacios</a>
            </div>
        </div>

        <!-- ALERTAS DE OPERACION -->
        @if(session('success'))
            <div style="background-color: #dcfce7; color: #166534; border: 1px solid #86efac; padding: 1rem 1.25rem; border-radius: 10px; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                <div><strong>Operacion exitosa:</strong> {{ session('success') }}</div>
                <button type="button" onclick="this.parentElement.style.display='none'" style="background: none; border: none; font-size: 1.4rem; color: #166534; cursor: pointer;">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div style="background-color: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; padding: 1rem 1.25rem; border-radius: 10px; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                <div><strong>Atencion:</strong> {{ session('error') }}</div>
                <button type="button" onclick="this.parentElement.style.display='none'" style="background: none; border: none; font-size: 1.4rem; color: #991b1b; cursor: pointer;">&times;</button>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div style="background-color: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; padding: 1rem 1.25rem; border-radius: 10px; margin-bottom: 1.5rem;">
                <strong>Por favor verifica los siguientes campos:</strong>
                <ul style="margin: 0.4rem 0 0 1.2rem; padding: 0;">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- METRICAS RAPIDAS -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1rem; margin-bottom: 1.8rem;">
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-left: 4px solid #d97706; border-radius: 10px; padding: 1.1rem 1.25rem; box-shadow: 0 2px 6px rgba(0,0,0,0.03);">
                <span style="font-size: 0.78rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Pagos por Verificar</span>
                <div style="font-size: 1.8rem; font-weight: 800; color: #b45309; margin-top: 0.2rem;">{{ $solicitudesPendientes->count() }}</div>
                <span style="font-size: 0.8rem; color: #64748b;">Solicitudes Pago Movil Auditorio</span>
            </div>
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-left: 4px solid #16a34a; border-radius: 10px; padding: 1.1rem 1.25rem; box-shadow: 0 2px 6px rgba(0,0,0,0.03);">
                <span style="font-size: 0.78rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Butacas Auditorio Asignadas</span>
                <div style="font-size: 1.8rem; font-weight: 800; color: #15803d; margin-top: 0.2rem;">{{ $ticketsAuditorioActivos->count() }}</div>
                <span style="font-size: 0.8rem; color: #64748b;">Con QR y correo confirmados</span>
            </div>
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-left: 4px solid #2563eb; border-radius: 10px; padding: 1.1rem 1.25rem; box-shadow: 0 2px 6px rgba(0,0,0,0.03);">
                <span style="font-size: 0.78rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Espacios Universitarios</span>
                <div style="font-size: 1.8rem; font-weight: 800; color: #1d3557; margin-top: 0.2rem;">{{ $salones->count() }}</div>
                <span style="font-size: 0.8rem; color: #64748b;">{{ $salones->where('estado', 'disponible')->count() }} abiertos / {{ $salones->where('estado', '!=', 'disponible')->count() }} cerrados o en uso</span>
            </div>
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-left: 4px solid #7c3aed; border-radius: 10px; padding: 1.1rem 1.25rem; box-shadow: 0 2px 6px rgba(0,0,0,0.03);">
                <span style="font-size: 0.78rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Cursos, Talleres y Eventos</span>
                <div style="font-size: 1.8rem; font-weight: 800; color: #4c1d95; margin-top: 0.2rem;">{{ $cursos->count() }}</div>
                <span style="font-size: 0.8rem; color: #64748b;">{{ $profesores->count() }} profesores registrados</span>
            </div>
        </div>

        <!-- NAVEGACION POR PESTANAS -->
        <div class="admin-tabs" style="display: flex; gap: 0.5rem; flex-wrap: wrap; border-bottom: 2px solid #e2e8f0; margin-bottom: 2rem; padding-bottom: 0.5rem;">
            <button type="button" class="admin-tab-btn active" data-tab="pagos" onclick="switchAdminTab('pagos')">
                Auditorio: Pagos y Asignacion Manual ({{ $solicitudesPendientes->count() }})
            </button>
            <button type="button" class="admin-tab-btn" data-tab="espacios" onclick="switchAdminTab('espacios')">
                Espacios y Salones ({{ $salones->count() }})
            </button>
            <button type="button" class="admin-tab-btn" data-tab="cursos" onclick="switchAdminTab('cursos')">
                Cursos, Talleres y Eventos ({{ $cursos->count() }})
            </button>
            <button type="button" class="admin-tab-btn" data-tab="profesores" onclick="switchAdminTab('profesores')">
                Profesores ({{ $profesores->count() }})
            </button>
        </div>

        <!-- =========================================================================
             PESTANA 1: AUDITORIO - SOLICITUDES DE PAGO MOVIL Y ASIGNACION MANUAL
             ========================================================================= -->
        <div id="tab-pagos" class="admin-tab-panel active">

            <!-- SECCION A: SOLICITUDES PENDIENTES DE PAGO MOVIL -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 1rem;">
                    <div>
                        <h3 style="margin: 0; color: #1d3557; font-size: 1.25rem;">Solicitudes de Pago Movil Pendientes (Pasarela Web)</h3>
                        <p style="margin: 0.25rem 0 0 0; font-size: 0.88rem; color: #64748b;">Verifica que el numero de referencia y el monto concuerden con el Pago Movil recibido. Al aprobar, se activa la butaca y se envia automaticamente el correo con QR y enlace a Google Calendar.</p>
                    </div>
                    <span style="background: #fef3c7; color: #92400e; padding: 0.35rem 0.85rem; border-radius: 50px; font-size: 0.82rem; font-weight: 700;">
                        {{ $solicitudesPendientes->count() }} pendientes
                    </span>
                </div>

                @if($solicitudesPendientes->count() > 0)
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.88rem;">
                            <thead>
                                <tr style="background: #f8fafc; color: #334155; text-align: left; border-bottom: 2px solid #e2e8f0;">
                                    <th style="padding: 0.85rem;">Estudiante</th>
                                    <th style="padding: 0.85rem;">Evento / Horario</th>
                                    <th style="padding: 0.85rem;">Butaca</th>
                                    <th style="padding: 0.85rem;">Datos Pago Movil</th>
                                    <th style="padding: 0.85rem;">Referencia</th>
                                    <th style="padding: 0.85rem;">Monto</th>
                                    <th style="padding: 0.85rem; text-align: right;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($solicitudesPendientes as $sol)
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td style="padding: 0.85rem;">
                                            <strong style="color: #0f172a; display: block;">{{ $sol->usuario->nombreCompleto() }}</strong>
                                            <span style="color: #64748b; font-size: 0.8rem;">C.I: {{ $sol->usuario->cedula }} | {{ $sol->usuario->email }}</span>
                                        </td>
                                        <td style="padding: 0.85rem;">
                                            <strong style="color: #1d3557; display: block;">{{ $sol->funcion->curso->nombre }}</strong>
                                            <span style="color: #64748b; font-size: 0.8rem;">{{ $sol->funcion->diaDisplay() }} ({{ $sol->funcion->horario() }})</span>
                                        </td>
                                        <td style="padding: 0.85rem;">
                                            <span style="background: #fef3c7; color: #92400e; border: 1px solid #f59e0b; padding: 0.25rem 0.6rem; border-radius: 6px; font-weight: 800;">
                                                {{ $sol->butaca->etiqueta }}
                                            </span>
                                        </td>
                                        <td style="padding: 0.85rem;">
                                            <span style="display: block; color: #334155;"><strong>Banco:</strong> {{ $sol->banco_pago ?: 'No indicado' }}</span>
                                            <span style="display: block; color: #64748b; font-size: 0.8rem;"><strong>Tel:</strong> {{ $sol->telefono_pago ?: 'No indicado' }}</span>
                                        </td>
                                        <td style="padding: 0.85rem;">
                                            <span style="font-family: monospace; font-size: 0.98rem; font-weight: 800; background: #eff6ff; color: #1e40af; padding: 0.25rem 0.55rem; border-radius: 6px; border: 1px solid #bfdbfe;">
                                                #{{ $sol->referencia_pago }}
                                            </span>
                                        </td>
                                        <td style="padding: 0.85rem; font-weight: 800; color: #0f172a;">
                                            ${{ number_format($sol->monto_pagado, 2) }}
                                        </td>
                                        <td style="padding: 0.85rem; text-align: right; white-space: nowrap;">
                                            <form action="{{ route('admin.tickets.aprobar', $sol->id) }}" method="POST" style="display: inline-block; margin-right: 0.4rem;">
                                                @csrf
                                                <button type="submit" class="btn btn-primary btn-sm" style="background-color: #16a34a; border-color: #16a34a; font-size: 0.8rem; padding: 0.45rem 0.8rem;">
                                                    Aprobar y Enviar QR
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.tickets.rechazar', $sol->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('¿Rechazar esta solicitud y liberar la butaca {{ $sol->butaca->etiqueta }}?');">
                                                @csrf
                                                <button type="submit" class="btn btn-outline btn-sm" style="color: #dc2626; border-color: #dc2626; font-size: 0.8rem; padding: 0.45rem 0.8rem;">
                                                    Rechazar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 1.75rem; text-align: center; color: #64748b;">
                        No hay solicitudes de Pago Movil pendientes de verificacion en este momento.
                    </div>
                @endif
            </div>

            <!-- SECCION B: ASIGNACION MANUAL DE BUTACA (TAQUILLA PRESENCIAL / EFECTIVO) -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                <div style="margin-bottom: 1.25rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 1rem;">
                    <span style="display: inline-block; background: #dcfce7; color: #166534; font-size: 0.72rem; font-weight: 800; padding: 0.2rem 0.65rem; border-radius: 50px; text-transform: uppercase; margin-bottom: 0.35rem;">Taquilla Presencial / Efectivo</span>
                    <h3 style="margin: 0; color: #1d3557; font-size: 1.25rem;">Asignar Butaca de Auditorio Manualmente</h3>
                    <p style="margin: 0.25rem 0 0 0; font-size: 0.88rem; color: #64748b;">Cuando un estudiante pague en efectivo o confirme su Pago Movil presencialmente en taquilla, selecciona aqui al estudiante, el evento y la butaca. El sistema creara el ticket activo inmediatamente, lo mostrara en su perfil con opcion de calendario y le enviara el correo automatizado con su codigo QR.</p>
                </div>

                <form action="{{ route('admin.auditorio.asignar') }}" method="POST">
                    @csrf
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; align-items: end;">
                        <div>
                            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">1. Estudiante</label>
                            <select name="usuario_id" required style="width: 100%; padding: 0.65rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem;">
                                <option value="">Seleccione un estudiante...</option>
                                @foreach($estudiantes as $est)
                                    <option value="{{ $est->id }}">{{ $est->cedula }} - {{ $est->nombreCompleto() }} ({{ $est->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">2. Evento del Auditorio</label>
                            <select name="funcion_id" id="manual_funcion_select" required onchange="filtrarButacasEvento()" style="width: 100%; padding: 0.65rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem;">
                                <option value="">Seleccione un evento...</option>
                                @foreach($eventosFunciones as $ef)
                                    <option value="{{ $ef->id }}">
                                        {{ $ef->curso->nombre }} - {{ $ef->diaDisplay() }} ({{ $ef->horario() }}) - ${{ number_format($ef->curso->precio, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">3. Butaca Disponible</label>
                            <select name="butaca_id" id="manual_butaca_select" required style="width: 100%; padding: 0.65rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem;">
                                <option value="">Primero elija un evento...</option>
                            </select>
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">4. Referencia Pago Movil / Recibo Efectivo</label>
                            <input type="text" name="referencia_pago" placeholder="Ej: 123456 o EFECTIVO-001" required style="width: 100%; padding: 0.65rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem;">
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">5. Metodo / Banco Origen</label>
                            <input type="text" name="banco_pago" value="Taquilla Presencial (Efectivo / Pago Movil)" style="width: 100%; padding: 0.65rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem;">
                        </div>

                        <div>
                            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.7rem 1rem; font-weight: 700;">
                                Asignar Butaca y Enviar Correo QR
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- SECCION C: BUTACAS DEL AUDITORIO YA CONFIRMADAS -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                <h3 style="margin: 0 0 1rem 0; color: #1d3557; font-size: 1.15rem;">Entradas de Auditorio Confirmadas y Activas ({{ $ticketsAuditorioActivos->count() }})</h3>
                @if($ticketsAuditorioActivos->count() > 0)
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.86rem;">
                            <thead>
                                <tr style="background: #f8fafc; color: #475569; text-align: left; border-bottom: 1px solid #e2e8f0;">
                                    <th style="padding: 0.7rem;">Codigo QR</th>
                                    <th style="padding: 0.7rem;">Estudiante</th>
                                    <th style="padding: 0.7rem;">Evento</th>
                                    <th style="padding: 0.7rem;">Butaca</th>
                                    <th style="padding: 0.7rem;">Referencia</th>
                                    <th style="padding: 0.7rem;">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ticketsAuditorioActivos as $ta)
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding: 0.7rem; font-family: monospace; font-weight: 700; color: #1d3557;">{{ $ta->codigo_ticket }}</td>
                                        <td style="padding: 0.7rem;">{{ $ta->usuario->nombreCompleto() }} ({{ $ta->usuario->cedula }})</td>
                                        <td style="padding: 0.7rem;">{{ $ta->funcion->curso->nombre }}</td>
                                        <td style="padding: 0.7rem;"><strong>{{ $ta->butaca->etiqueta }}</strong></td>
                                        <td style="padding: 0.7rem;">{{ $ta->referencia_pago ?: 'N/A' }}</td>
                                        <td style="padding: 0.7rem;">
                                            <span style="background: #dcfce7; color: #166534; padding: 0.2rem 0.6rem; border-radius: 50px; font-size: 0.76rem; font-weight: 700;">Confirmado</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p style="margin: 0; color: #64748b; font-size: 0.9rem;">Aun no hay butacas confirmadas en el Auditorio.</p>
                @endif
            </div>
        </div>

        <!-- =========================================================================
             PESTANA 2: GESTION DE ESPACIOS Y SALONES EN TIEMPO REAL
             ========================================================================= -->
        <div id="tab-espacios" class="admin-tab-panel" style="display: none;">
            <div style="margin-bottom: 1.25rem;">
                <h3 style="margin: 0; color: #1d3557;">Control de Espacios y Salones en Tiempo Real</h3>
                <p style="margin: 0.25rem 0 0 0; color: #64748b; font-size: 0.9rem;">Cambia cualquier salon de <strong>Disponible (Abierto)</strong> a <strong>Cerrado / Mantenimiento</strong> o <strong>En Uso</strong>, actualiza su equipamiento o sube una nueva fotografia sin tocar codigo.</p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.5rem;">
                @foreach($salones as $salon)
                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                        <div style="position: relative; height: 155px; background: #0f172a;">
                            <img src="{{ asset($salon->foto ?: 'images/salones/aula_101.jpg') }}" alt="{{ $salon->nombre }}" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.9;">
                            <span style="position: absolute; top: 10px; left: 10px; background: rgba(15,23,42,0.85); color: #ffffff; font-size: 0.78rem; font-weight: 700; padding: 0.25rem 0.65rem; border-radius: 6px;">
                                {{ $salon->codigo }} ({{ $salon->capacidad }} butacas)
                            </span>
                            <span style="position: absolute; top: 10px; right: 10px; padding: 0.25rem 0.7rem; border-radius: 50px; font-size: 0.76rem; font-weight: 800;
                                @if($salon->estado === 'disponible') background: #dcfce7; color: #166534;
                                @elseif($salon->estado === 'en_uso') background: #fef3c7; color: #92400e;
                                @else background: #fee2e2; color: #991b1b; @endif">
                                @if($salon->estado === 'disponible') Abierto / Disponible
                                @elseif($salon->estado === 'en_uso') En Uso
                                @else Cerrado / Mantenimiento @endif
                            </span>
                        </div>

                        <form action="{{ route('admin.salones.update', $salon->id) }}" method="POST" enctype="multipart/form-data" style="padding: 1.25rem;">
                            @csrf
                            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 0.65rem; margin-bottom: 0.75rem;">
                                <div>
                                    <label style="display: block; font-size: 0.76rem; font-weight: 700; color: #475569; margin-bottom: 0.25rem;">Codigo</label>
                                    <input type="text" name="codigo" value="{{ $salon->codigo }}" required style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.85rem;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.76rem; font-weight: 700; color: #475569; margin-bottom: 0.25rem;">Nombre del Espacio</label>
                                    <input type="text" name="nombre" value="{{ $salon->nombre }}" required style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.85rem;">
                                </div>
                            </div>

                            <div style="margin-bottom: 0.75rem;">
                                <label style="display: block; font-size: 0.76rem; font-weight: 700; color: #475569; margin-bottom: 0.25rem;">Estado Operativo del Espacio</label>
                                <select name="estado" required style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.86rem; font-weight: 600;">
                                    <option value="disponible" {{ $salon->estado === 'disponible' ? 'selected' : '' }}>Disponible (Abierto para reservas)</option>
                                    <option value="en_uso" {{ $salon->estado === 'en_uso' ? 'selected' : '' }}>En Uso Academico</option>
                                    <option value="mantenimiento" {{ $salon->estado === 'mantenimiento' ? 'selected' : '' }}>Cerrado / En Mantenimiento (Bloquea reservas)</option>
                                </select>
                            </div>

                            <div style="margin-bottom: 0.75rem;">
                                <label style="display: block; font-size: 0.76rem; font-weight: 700; color: #475569; margin-bottom: 0.25rem;">Equipamiento y Descripcion</label>
                                <textarea name="equipos" rows="2" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.84rem;">{{ $salon->equipos }}</textarea>
                            </div>

                            <div style="margin-bottom: 1rem;">
                                <label style="display: block; font-size: 0.76rem; font-weight: 700; color: #475569; margin-bottom: 0.25rem;">Cambiar Imagen del Espacio (Opcional)</label>
                                <input type="file" name="foto" accept="image/*" style="width: 100%; font-size: 0.8rem; color: #475569;">
                            </div>

                            <button type="submit" class="btn btn-primary btn-sm" style="width: 100%; padding: 0.6rem; font-weight: 700;">
                                Guardar Cambios en {{ $salon->codigo }}
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- =========================================================================
             PESTANA 3: GESTION DE CURSOS, TALLERES Y EVENTOS DE AUDITORIO
             ========================================================================= -->
        <div id="tab-cursos" class="admin-tab-panel" style="display: none;">

            <!-- FORMULARIO PARA CREAR NUEVO CURSO / TALLER / EVENTO -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                <h3 style="margin: 0 0 0.3rem 0; color: #1d3557; font-size: 1.2rem;">Publicar Nuevo Curso, Taller o Evento de Auditorio</h3>
                <p style="margin: 0 0 1.2rem 0; color: #64748b; font-size: 0.86rem;">Crea una nueva actividad con su horario inicial, salon asignado, profesor, precio e imagen de portada.</p>

                <form action="{{ route('admin.cursos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.9rem; margin-bottom: 0.9rem;">
                        <div>
                            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.25rem;">Codigo</label>
                            <input type="text" name="codigo" placeholder="Ej: EVE-004 o CUR-205" required style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.86rem;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.25rem;">Nombre de la Actividad</label>
                            <input type="text" name="nombre" placeholder="Titulo del curso, taller o evento" required style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.86rem;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.25rem;">Modalidad / Tipo</label>
                            <select name="tipo" required style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.86rem;">
                                <option value="curso">Curso Academico (Gratuito)</option>
                                <option value="taller">Taller Practico (Gratuito)</option>
                                <option value="clase">Clase Magistral (Gratuito)</option>
                                <option value="evento">Evento / Graduacion en Auditorio (De Pago)</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.25rem;">Precio en USD (0 = Gratuito)</label>
                            <input type="number" step="0.01" min="0" name="precio" value="0.00" required style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.86rem;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.9rem; margin-bottom: 0.9rem;">
                        <div>
                            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.25rem;">Salon / Espacio</label>
                            <select name="salon_id" required style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.86rem;">
                                @foreach($salones as $s)
                                    <option value="{{ $s->id }}">{{ $s->codigo }} - {{ $s->nombre }} ({{ $s->capacidad }} butacas)</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.25rem;">Profesor / Ponente</label>
                            <select name="profesor_id" required style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.86rem;">
                                @foreach($profesores as $p)
                                    <option value="{{ $p->id }}">{{ $p->nombreCompleto() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.25rem;">Dia de la Funcion</label>
                            <select name="dia_semana" required style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.86rem;">
                                <option value="lunes">Lunes</option>
                                <option value="martes">Martes</option>
                                <option value="miercoles">Miercoles</option>
                                <option value="jueves">Jueves</option>
                                <option value="viernes">Viernes</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.25rem;">Hora Inicio / Fin</label>
                            <div style="display: flex; gap: 0.4rem;">
                                <input type="time" name="hora_inicio" value="09:00" required style="width: 50%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.84rem;">
                                <input type="time" name="hora_fin" value="12:00" required style="width: 50%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.84rem;">
                            </div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 0.9rem; margin-bottom: 1rem; align-items: end;">
                        <div>
                            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.25rem;">Descripcion (Soporta multiples parrafos y saltos de linea)</label>
                            <textarea name="descripcion" rows="3" placeholder="Escribe la descripcion detallada del curso, taller o evento. Puedes presionar Enter para separar en varios parrafos..." style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.86rem; font-family: inherit; resize: vertical;"></textarea>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.25rem;">Imagen de Portada</label>
                            <input type="file" name="foto" accept="image/*" style="width: 100%; font-size: 0.8rem;">
                        </div>
                        <div>
                            <input type="hidden" name="semanas_totales" value="6">
                            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.65rem; font-weight: 700;">
                                Crear Actividad
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- TARJETAS DE EDICION EN TIEMPO REAL PARA CADA CURSO / TALLER / EVENTO -->
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap: 1.5rem;">
                @foreach($cursos as $curso)
                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                        <div style="position: relative; height: 145px; background: #0f172a;">
                            <img src="{{ asset($curso->foto ?: 'images/carousel/edificio_nautica.jpg') }}" alt="{{ $curso->nombre }}" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.88;">
                            <span style="position: absolute; top: 10px; left: 10px; background: rgba(15,23,42,0.85); color: #ffffff; font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 6px;">
                                {{ $curso->codigo }} | {{ $curso->tipoBadge() }}
                            </span>
                            <span style="position: absolute; top: 10px; right: 10px; background: {{ $curso->precio > 0 ? '#eab308' : '#16a34a' }}; color: {{ $curso->precio > 0 ? '#0f172a' : '#ffffff' }}; font-size: 0.78rem; font-weight: 800; padding: 0.2rem 0.65rem; border-radius: 50px;">
                                {{ $curso->precio > 0 ? '$' . number_format($curso->precio, 2) . ' USD' : 'Gratuito' }}
                            </span>
                        </div>

                        <form action="{{ route('admin.cursos.update', $curso->id) }}" method="POST" enctype="multipart/form-data" style="padding: 1.2rem;">
                            @csrf
                            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 0.6rem; margin-bottom: 0.65rem;">
                                <div>
                                    <label style="display: block; font-size: 0.74rem; font-weight: 700; color: #475569;">Codigo</label>
                                    <input type="text" name="codigo" value="{{ $curso->codigo }}" required style="width: 100%; padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.84rem;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.74rem; font-weight: 700; color: #475569;">Titulo</label>
                                    <input type="text" name="nombre" value="{{ $curso->nombre }}" required style="width: 100%; padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.84rem;">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.6rem; margin-bottom: 0.65rem;">
                                <div>
                                    <label style="display: block; font-size: 0.74rem; font-weight: 700; color: #475569;">Tipo</label>
                                    <select name="tipo" style="width: 100%; padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.82rem;">
                                        <option value="curso" {{ $curso->tipo === 'curso' ? 'selected' : '' }}>Curso</option>
                                        <option value="taller" {{ $curso->tipo === 'taller' ? 'selected' : '' }}>Taller</option>
                                        <option value="clase" {{ $curso->tipo === 'clase' ? 'selected' : '' }}>Clase</option>
                                        <option value="evento" {{ $curso->tipo === 'evento' ? 'selected' : '' }}>Evento Auditorio</option>
                                    </select>
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.74rem; font-weight: 700; color: #475569;">Precio (USD)</label>
                                    <input type="number" step="0.01" min="0" name="precio" value="{{ $curso->precio }}" style="width: 100%; padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.84rem;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.74rem; font-weight: 700; color: #475569;">Semanas</label>
                                    <input type="number" min="1" max="52" name="semanas_totales" value="{{ $curso->semanas_totales }}" style="width: 100%; padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.84rem;">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.6rem; margin-bottom: 0.65rem;">
                                <div>
                                    <label style="display: block; font-size: 0.74rem; font-weight: 700; color: #475569;">Salon Asignado</label>
                                    <select name="salon_id" style="width: 100%; padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.82rem;">
                                        @foreach($salones as $s)
                                            <option value="{{ $s->id }}" {{ $curso->salon_id == $s->id ? 'selected' : '' }}>{{ $s->codigo }} - {{ $s->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.74rem; font-weight: 700; color: #475569;">Profesor / Ponente</label>
                                    <select name="profesor_id" style="width: 100%; padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.82rem;">
                                        @foreach($profesores as $p)
                                            <option value="{{ $p->id }}" {{ $curso->profesor_id == $p->id ? 'selected' : '' }}>{{ $p->nombreCompleto() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div style="margin-bottom: 0.65rem;">
                                <label style="display: block; font-size: 0.74rem; font-weight: 700; color: #475569;">Descripcion (Soporta saltos de linea)</label>
                                <textarea name="descripcion" rows="3" style="width: 100%; padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.82rem; font-family: inherit; resize: vertical;">{{ $curso->descripcion }}</textarea>
                            </div>

                            <div style="margin-bottom: 0.75rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.65rem;">
                                <label style="display: block; font-size: 0.74rem; font-weight: 700; color: #334155; margin-bottom: 0.45rem;">Horario(s) de la Actividad</label>
                                @if($curso->funciones->count() > 0)
                                    @foreach($curso->funciones as $funcion)
                                        <div style="display: grid; grid-template-columns: 1.1fr 1fr 1fr; gap: 0.4rem; margin-bottom: 0.4rem;">
                                            <select name="funciones[{{ $funcion->id }}][dia_semana]" required style="width: 100%; padding: 0.4rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.8rem; background: #fff;">
                                                <option value="lunes" {{ $funcion->dia_semana === 'lunes' ? 'selected' : '' }}>Lunes</option>
                                                <option value="martes" {{ $funcion->dia_semana === 'martes' ? 'selected' : '' }}>Martes</option>
                                                <option value="miercoles" {{ $funcion->dia_semana === 'miercoles' ? 'selected' : '' }}>Miercoles</option>
                                                <option value="jueves" {{ $funcion->dia_semana === 'jueves' ? 'selected' : '' }}>Jueves</option>
                                                <option value="viernes" {{ $funcion->dia_semana === 'viernes' ? 'selected' : '' }}>Viernes</option>
                                            </select>
                                            <input type="time" name="funciones[{{ $funcion->id }}][hora_inicio]" value="{{ substr($funcion->hora_inicio, 0, 5) }}" required style="width: 100%; padding: 0.4rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.8rem; background: #fff;">
                                            <input type="time" name="funciones[{{ $funcion->id }}][hora_fin]" value="{{ substr($funcion->hora_fin, 0, 5) }}" required style="width: 100%; padding: 0.4rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.8rem; background: #fff;">
                                        </div>
                                    @endforeach
                                @else
                                    <div style="display: grid; grid-template-columns: 1.1fr 1fr 1fr; gap: 0.4rem;">
                                        <select name="nuevo_dia_semana" style="width: 100%; padding: 0.4rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.8rem; background: #fff;">
                                            <option value="">Dia...</option>
                                            <option value="lunes">Lunes</option>
                                            <option value="martes">Martes</option>
                                            <option value="miercoles">Miercoles</option>
                                            <option value="jueves">Jueves</option>
                                            <option value="viernes">Viernes</option>
                                        </select>
                                        <input type="time" name="nueva_hora_inicio" value="08:00" style="width: 100%; padding: 0.4rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.8rem; background: #fff;">
                                        <input type="time" name="nueva_hora_fin" value="10:00" style="width: 100%; padding: 0.4rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.8rem; background: #fff;">
                                    </div>
                                @endif
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.6rem; margin-bottom: 0.9rem; align-items: center;">
                                <div>
                                    <label style="display: block; font-size: 0.74rem; font-weight: 700; color: #475569;">Visibilidad</label>
                                    <select name="activo" style="width: 100%; padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.82rem;">
                                        <option value="1" {{ $curso->activo ? 'selected' : '' }}>Visible en Catalogo</option>
                                        <option value="0" {{ !$curso->activo ? 'selected' : '' }}>Oculto / Cerrado</option>
                                    </select>
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.74rem; font-weight: 700; color: #475569;">Nueva Portada</label>
                                    <input type="file" name="foto" accept="image/*" style="width: 100%; font-size: 0.75rem;">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-sm" style="width: 100%; padding: 0.55rem; font-weight: 700;">
                                Actualizar {{ $curso->codigo }}
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- =========================================================================
             PESTANA 4: GESTION DE PROFESORES EN TIEMPO REAL
             ========================================================================= -->
        <div id="tab-profesores" class="admin-tab-panel" style="display: none;">

            <!-- FORMULARIO PARA AGREGAR NUEVO PROFESOR -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                <h3 style="margin: 0 0 0.3rem 0; color: #1d3557; font-size: 1.2rem;">Registrar Nuevo Profesor</h3>
                <p style="margin: 0 0 1.2rem 0; color: #64748b; font-size: 0.86rem;">Incorpora un nuevo docente al directorio publico con su perfil academico y especialidades.</p>

                <form action="{{ route('admin.profesores.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.85rem; margin-bottom: 0.85rem;">
                        <div>
                            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.25rem;">Cedula</label>
                            <input type="text" name="cedula" placeholder="Ej: 19100209" required style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.86rem;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.25rem;">Nombre</label>
                            <input type="text" name="nombre" required style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.86rem;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.25rem;">Apellido</label>
                            <input type="text" name="apellido" required style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.86rem;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.25rem;">Correo Institucional</label>
                            <input type="email" name="email" maxlength="255" placeholder="profesor@unispace.edu.ve" required style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.86rem;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.25rem;">Telefono</label>
                            <input type="text" name="telefono" placeholder="04140000000" style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.86rem;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.25rem;">Foto de Perfil (Opcional)</label>
                            <input type="file" name="foto_perfil" accept="image/*" style="width: 100%; font-size: 0.8rem;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 0.85rem; align-items: end;">
                        <div>
                            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.25rem;">Biografia y Perfil Academico (Soporta parrafos y saltos de linea)</label>
                            <textarea name="biografia" rows="3" placeholder="Especialidad, titulos y experiencia..." style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.86rem; font-family: inherit; resize: vertical;"></textarea>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.25rem;">Areas de Interes</label>
                            <input type="text" name="gustos" placeholder="Ej: Navegacion, IA, Logistica" style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.86rem;">
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.65rem; font-weight: 700;">
                                Registrar Profesor
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- TARJETAS DE EDICION DE PROFESORES -->
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.5rem;">
                @foreach($profesores as $prof)
                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04); min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; padding-bottom: 0.85rem; border-bottom: 1px solid #f1f5f9;">
                            @if($prof->fotoPerfilUrl())
                                <img src="{{ $prof->fotoPerfilUrl() }}" alt="{{ $prof->nombreCompleto() }}" style="width: 58px; height: 58px; border-radius: 50%; object-fit: cover; border: 2px solid #cbd5e1; flex-shrink: 0;">
                            @else
                                <div style="width: 58px; height: 58px; border-radius: 50%; background: #1d3557; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.1rem; flex-shrink: 0;">
                                    {{ strtoupper(substr($prof->nombre, 0, 1) . substr($prof->apellido, 0, 1)) }}
                                </div>
                            @endif
                            <div style="min-width: 0;">
                                <h4 style="margin: 0; color: #0f172a; font-size: 1.05rem;">{{ $prof->nombreCompleto() }}</h4>
                                <span style="font-size: 0.8rem; color: #64748b;">C.I: {{ $prof->cedula }} | {{ $prof->cursos ? $prof->cursos->count() : 0 }} actividades</span>
                            </div>
                        </div>

                        <form action="{{ route('admin.profesores.update', $prof->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.6rem; margin-bottom: 0.65rem;">
                                <div>
                                    <label style="display: block; font-size: 0.74rem; font-weight: 700; color: #475569;">Nombre</label>
                                    <input type="text" name="nombre" value="{{ $prof->nombre }}" required style="width: 100%; padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.84rem;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.74rem; font-weight: 700; color: #475569;">Apellido</label>
                                    <input type="text" name="apellido" value="{{ $prof->apellido }}" required style="width: 100%; padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.84rem;">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.6rem; margin-bottom: 0.65rem;">
                                <div>
                                    <label style="display: block; font-size: 0.74rem; font-weight: 700; color: #475569;">Correo</label>
                                    <input type="email" name="email" value="{{ $prof->email }}" maxlength="255" required style="width: 100%; padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.84rem;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.74rem; font-weight: 700; color: #475569;">Telefono</label>
                                    <input type="text" name="telefono" value="{{ $prof->telefono }}" style="width: 100%; padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.84rem;">
                                </div>
                            </div>

                            <div style="margin-bottom: 0.65rem;">
                                <label style="display: block; font-size: 0.74rem; font-weight: 700; color: #475569;">Foto de Perfil (Opcional)</label>
                                <input type="file" name="foto_perfil" accept="image/*" style="width: 100%; font-size: 0.75rem;">
                            </div>

                            <div style="margin-bottom: 0.65rem;">
                                <label style="display: block; font-size: 0.74rem; font-weight: 700; color: #475569;">Biografia Academica (Soporta parrafos)</label>
                                <textarea name="biografia" rows="3" style="width: 100%; padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.82rem; font-family: inherit; resize: vertical;">{{ $prof->biografia }}</textarea>
                            </div>

                            <div style="margin-bottom: 0.9rem;">
                                <label style="display: block; font-size: 0.74rem; font-weight: 700; color: #475569;">Areas de Interes / Especialidades</label>
                                <input type="text" name="gustos" value="{{ $prof->gustos }}" style="width: 100%; padding: 0.45rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.82rem;">
                            </div>

                            <button type="submit" class="btn btn-primary btn-sm" style="width: 100%; padding: 0.55rem; font-weight: 700;">
                                Guardar Datos del Profesor
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</section>

@php
    // Preparamos el mapa de butacas disponibles por cada funcion de evento del Auditorio
    $mapaButacasEventos = [];
    foreach ($eventosFunciones as $ef) {
        $ocupadasIds = $ef->tickets
            ->whereIn('estado', ['activo', 'pendiente'])
            ->pluck('butaca_id')
            ->toArray();

        $disponibles = [];
        if ($ef->curso && $ef->curso->salon) {
            foreach ($ef->curso->salon->butacas as $b) {
                if (!in_array($b->id, $ocupadasIds)) {
                    $disponibles[] = [
                        'id' => $b->id,
                        'etiqueta' => $b->etiqueta,
                    ];
                }
            }
        }
        $mapaButacasEventos[$ef->id] = $disponibles;
    }
@endphp

@endsection

@push('scripts')
<style>
.admin-tab-btn {
    background: #ffffff;
    color: #475569;
    border: 1px solid #cbd5e1;
    padding: 0.65rem 1.15rem;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.88rem;
    cursor: pointer;
    transition: all 0.2s ease;
}
.admin-tab-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
}
.admin-tab-btn.active {
    background: #1d3557;
    color: #ffffff;
    border-color: #1d3557;
}
</style>

<script>
    const butacasPorFuncion = @json($mapaButacasEventos);

    function switchAdminTab(tabName) {
        document.querySelectorAll('.admin-tab-panel').forEach(panel => {
            panel.style.display = 'none';
        });
        document.querySelectorAll('.admin-tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        const targetPanel = document.getElementById('tab-' + tabName);
        if (targetPanel) {
            targetPanel.style.display = 'block';
        }
        const targetBtn = document.querySelector('.admin-tab-btn[data-tab="' + tabName + '"]');
        if (targetBtn) {
            targetBtn.classList.add('active');
        }
    }

    function filtrarButacasEvento() {
        const funcionId = document.getElementById('manual_funcion_select').value;
        const selectButaca = document.getElementById('manual_butaca_select');
        selectButaca.innerHTML = '';

        if (!funcionId || !butacasPorFuncion[funcionId]) {
            selectButaca.innerHTML = '<option value="">Primero elija un evento...</option>';
            return;
        }

        const lista = butacasPorFuncion[funcionId];
        if (lista.length === 0) {
            selectButaca.innerHTML = '<option value="">Sin butacas disponibles</option>';
            return;
        }

        lista.forEach(b => {
            const opt = document.createElement('option');
            opt.value = b.id;
            opt.textContent = 'Butaca ' + b.etiqueta + ' (Disponible)';
            selectButaca.appendChild(opt);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const params = new URLSearchParams(window.location.search);
        const tab = params.get('tab');
        if (tab && ['pagos', 'espacios', 'cursos', 'profesores'].includes(tab)) {
            switchAdminTab(tab);
        }
    });
</script>
@endpush
