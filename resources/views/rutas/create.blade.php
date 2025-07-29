@extends('layouts.app')

@section('title', 'Crear Nueva Ruta')

@section('content')
<div class="container py-4">
    <h1>Crear Nueva Ruta</h1>
    <form action="{{ route('admin.rutas.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="codigo" class="form-label">Código</label>
            <input type="text" name="codigo" id="codigo" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="origen" class="form-label">Origen</label>
            <input type="text" name="origen" id="origen" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="destino" class="form-label">Destino</label>
            <input type="text" name="destino" id="destino" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="distancia" class="form-label">Distancia (km)</label>
            <input type="number" step="0.1" name="distancia" id="distancia" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <select name="estado" id="estado" class="form-select" required>
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Ruta</button>
        <a href="{{ route('admin.rutas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
