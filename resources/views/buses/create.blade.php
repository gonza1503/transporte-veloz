@extends('layouts.app')

@section('title', 'Registrar nuevo Bus')

@section('content')
<div class="container">
    <h1 class="mb-4">Registrar nuevo Bus</h1>

    <form method="POST" action="{{ route('admin.buses.store') }}">
        @csrf

        <div class="mb-3">
            <label for="placa" class="form-label">Placa</label>
            <input type="text" name="placa" id="placa" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="modelo" class="form-label">Modelo</label>
            <input type="text" name="modelo" id="modelo" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="anio" class="form-label">Año</label>
            <input type="number" name="anio" id="anio" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="capacidad" class="form-label">Capacidad</label>
            <input type="number" name="capacidad" id="capacidad" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="chofer" class="form-label">Nombre del Chofer</label>
            <input type="text" name="chofer" id="chofer" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <select name="estado" id="estado" class="form-select" required>
                <option value="activo">Activo</option>
                <option value="mantenimiento">Mantenimiento</option>
                <option value="ocupado">Ocupado</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Registrar Bus</button>
        <a href="{{ route('buses.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
