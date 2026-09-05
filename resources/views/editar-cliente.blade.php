<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar cliente</title>
</head>
<body>

    <h1>Editar cliente</h1>

    <form method="POST" action="/clientes/{{ $cliente->id }}">
        @csrf
        @method('PUT')

        <label>Nombre:</label>
        <input type="text" name="nombre" value="{{ $cliente->nombre }}">

        <br><br>

        <label>Apellido:</label>
        <input type="text" name="apellido" value="{{ $cliente->apellido }}">

        <br><br>

        <label>Teléfono:</label>
        <input type="text" name="telefono" value="{{ $cliente->telefono }}">

        <br><br>

        <label>Email:</label>
        <input type="email" name="email" value="{{ $cliente->email }}">

        <br><br>

        <button type="submit">Guardar cambios</button>
    </form>

</body>
</html>