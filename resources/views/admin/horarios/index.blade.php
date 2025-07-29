@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Horarios</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <a href="{{ route('admin.horarios.create') }}" class="btn btn-primary mb-3">
        Crear nuevo horario
    </a>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Ruta</th>
                    <th>Bus</th>
                    <th>Fecha</th>
                    <th>Hora Salida</th>
                    <th>Asientos Disponibles</th>
                    <th>Activo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($horarios as $horario)
                <tr>
                    <td>{{ $horario->ruta->nombre ?? 'Sin ruta' }}</td>
                    <td>{{ $horario->bus->placa ?? 'Sin bus' }}</td>
                    <td>{{ \Carbon\Carbon::parse($horario->fecha)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($horario->hora_salida)->format('H:i') }}</td>
                    <td>{{ $horario->asientos_disponibles }}</td>
                    <td>
                        @if($horario->activo)
                            <span class="badge bg-success">Sí</span>
                        @else
                            <span class="badge bg-danger">No</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.horarios.edit', $horario->id) }}" class="btn btn-sm btn-warning">Editar</a>

                        @if($horario->activo)
                        <form action="{{ route('admin.horarios.cancelar', $horario->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('¿Seguro que querés cancelar este horario?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-danger">Cancelar</button>
                        </form>
                        @else
                            <button class="btn btn-sm btn-secondary" disabled>Cancelado</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No hay horarios disponibles.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div>
        {{ $horarios->links() }}
    </div>
</div>
@endsection
