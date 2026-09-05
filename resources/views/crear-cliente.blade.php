<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear cliente</title>
</head>
<body>

    <h1>Nuevo cliente</h1>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
        
        <style>
            #mensaje-error {
                position: fixed;
                top: 20px;
                right: 20px;
                background: #dc3545;
                color: white;
                padding: 15px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
                z-index: 1000;
            }
        </style>

        <script>
            setTimeout(function () {
                document.getElementById('mensaje-error').style.display = 'none';
            }, 5000);
        </script>

    <form method="POST" action="/clientes">
    @csrf
        <label>Nombre:</label>
        <input type="text" name="nombre" value="{{ old('nombre') }}">

        <br><br>

        <label>Apellido:</label>
        <input type="text" name="apellido" value="{{ old('apellido') }}">

        <br><br>

        <label>Teléfono:</label>
        <input type="text" name="telefono" value="{{ old('telefono') }}">

        <br><br>

        <label>Email:</label>
        <input type="email" name="email" value="{{ old('email') }}">

        <br><br>

        <button type="submit">Guardar cliente</button>
    </form>

</body>
</html>