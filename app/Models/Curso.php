<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    protected $table = 'cursos';

    protected $fillable = [
        'profesor_id',
        'salon_id',
        'nombre',
        'codigo',
        'dscripcion',
        'foto',
        'tipo',
        'fecha_inicio',
        'fecha_fin',
        'semanas_totales',
        'precio',
        'activo',
    ];

    protected $casts = [
        'fecha_inicio'    => 'date',
        'fecha_fin'       => 'date',
        'precio'          => 'decimal:2',
        'activo'          => 'boolean',
    ];

    // ─── Relaciones ──────────────────────────────────────────────

    public function profesor()
    {
        return $this->belongsTo(Usuario::class, 'profesor_id');
    }

    public function salon()
    {
        return $this->belongsTo(Salon::class, 'salon_id');
    }

    public function funciones()
    {
        return $this->hasMany(Funcion::class, 'curso_id');
    }

    // ─── Helpers ─────────────────────────────────────────────────

    public function tipoBadge(): string
    {
        return match ($this->tipo) {
            'curso'   => 'Curso',
            'taller'  => 'Taller',
            'clase'   => 'Clase',
            'evento'  => 'Evento / Acto',
            default   => 'Clase',
        };
    }

    public function esEvento(): bool
    {
        return $this->tipo === 'evento' || (float) $this->precio > 0;
    }
}
