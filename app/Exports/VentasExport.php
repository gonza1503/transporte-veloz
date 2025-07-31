<?php

namespace App\Exports;

use App\Models\Venta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VentasExport implements FromCollection, WithHeadings, WithMapping
{
    protected $fechaInicio;
    protected $fechaFin;
    protected $empleadoId;

    public function __construct($fechaInicio, $fechaFin, $empleadoId = null)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->empleadoId = $empleadoId;
    }

    public function collection()
    {
        $query = Venta::with(['user', 'horario.ruta', 'tickets'])
                     ->whereBetween('fecha_venta', [$this->fechaInicio, $this->fechaFin]);

        if ($this->empleadoId) {
            $query->where('user_id', $this->empleadoId);
        }

        return $query->orderBy('fecha_venta', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Código Venta',
            'Empleado',
            'Ruta',
            'Fecha Viaje',
            'Hora Salida',
            'Cantidad Pasajes',
            'Total',
            'Estado',
            'Fecha Venta'
        ];
    }

    public function map($venta): array
    {
        return [
            $venta->codigo_venta,
            $venta->user->name,
            $venta->horario->ruta->codigo . ' - ' . $venta->horario->ruta->origen . ' → ' . $venta->horario->ruta->destino,
            $venta->horario->fecha,
            $venta->horario->hora_salida,
            $venta->cantidad_pasajes,
            $venta->total,
            ucfirst($venta->estado),
            $venta->fecha_venta->format('d/m/Y H:i')
        ];
    }
}