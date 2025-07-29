@extends('layouts.app')

@section('title', 'Lista de Empleados')

@section('content')
<div class="container py-4">
    <h1>Empleados</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role ?? 'Sin rol' }}</td>
                <td>
                    <!-- Botones editar, eliminar, etc -->
                    <a href="#" class="btn btn-sm btn-primary">Editar</a>
                    <a href="#" class="btn btn-sm btn-danger">Eliminar</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $users->links() }} <!-- Paginación si usas -->
</div>
@endsection
