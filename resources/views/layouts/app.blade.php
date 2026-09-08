<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <title>@yield('title', 'Gestión')</title>

    <link rel="icon" type="image/png" href="{{ asset('logo-gestion.png') }}">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f6f8;
            color: #333;
        }

        header {
            background-color: white;
            border-bottom: 1px solid #ddd;
        }

        .barra-superior {
            display: flex;
            align-items: center;
            padding: 15px 30px;
        }

        .encabezado {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }

        .titulo {
            font-size: 26px;
            font-weight: bold;
        }

        nav {
            display: flex;
            gap: 5px;
            padding: 0 30px;
        }

        nav a {
            padding: 12px 18px;
            text-decoration: none;
            color: #555;
            border-radius: 6px 6px 0 0;
            transition: 0.2s;
            background-color: #eeeeee;
        }

        nav a:hover {
            background-color: #f0f0f0;
            color: #111;
        }

        nav a.activo {
            background-color: #eeeeee;
            color: #111;
            font-weight: bold;
            transform: translateY(-6px);
        }

        main {
            padding: 30px;
        }
    </style>
</head>

<body>

    <header>

        <div class="barra-superior">

            <div class="encabezado">

                <img
                    src="{{ asset('logo-gestion.png') }}"
                    alt="Logo"
                    class="logo"
                >

                <div class="titulo">
                    Gestión
                </div>

            </div>

        </div>

        <nav>
            <a href="/" class="{{ request()->is('/') ? 'activo' : '' }}">Inicio</a>

            <a href="/clientes" class="{{ request()->is('clientes*') ? 'activo' : '' }}">Clientes</a>

            <a href="/proveedores" class="{{ request()->is('proveedores*') ? 'activo' : '' }}">Proveedores</a>

            <a href="/productos" class="{{ request()->is('productos*') ? 'activo' : '' }}">Productos</a>

            <a href="/ventas" class="{{ request()->is('ventas*') ? 'activo' : '' }}">Ventas</a>

            <a href="/compras" class="{{ request()->is('compras*') ? 'activo' : '' }}">Compras</a>

            <a href="/caja" class="{{ request()->is('caja*') ? 'activo' : '' }}">Caja</a>

            <a href="/stock" class="{{ request()->is('stock*') ? 'activo' : '' }}">Stock</a>

            <a href="/reportes" class="{{ request()->is('reportes*') ? 'activo' : '' }}">Reportes</a>
        </nav>

    </header>

    <main>
        @yield('content')
    </main>

</body>
</html>