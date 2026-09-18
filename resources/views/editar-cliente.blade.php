@extends('layouts.app')

@section('title', 'Editar cliente')

@section('content')

    <style>
        
        .formulario-cliente { 
            max-width: 760px; 
        }

        .grupo-cliente { 
            margin: 0 0 24px; 
            padding: 20px; 
            border: 1px solid #dddddd; 
            background: #fafafa; 
        }
        
        .campo-cliente { 
            display: flex; 
            flex-direction: column; 
            gap: 6px; 
            margin-bottom: 16px; 
        }
        
        .campo-cliente:last-child { margin-bottom: 0; }
        
        .campo-cliente input { 
            max-width: 500px; 
            padding: 9px 10px; 
            border: 1px solid #cccccc; 
            border-radius: 4px; 
            font: inherit; 
        }
        
        .campo-cliente select { 
            max-width: 500px; 
            padding: 9px 10px; 
            border: 1px solid #cccccc; 
            border-radius: 4px; 
            font: inherit; 
        }
        
        .boton-cliente { 
            display: inline-block; 
            padding: 11px 16px; 
            background-color:  #2563eb; 
            color: black; 
            text-decoration: none; 
            border: 0; 
            border-radius: 6px; 
            cursor: pointer; 
            font-size: 14px; 
            margin-right: 5px; 
            font-weight: bold; 
            transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }
        
        .boton-cliente:hover { 
            background: #1d4ed8 !important;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3) !important;
            transform: translateY(-2px) !important;
         }
        
        .errores-formulario { 
            margin-bottom: 24px; 
            padding: 14px 18px; 
            border: 1px solid #dc3545; 
            background: #f8d7da; 
            color: #842029; 
        }
        
        .errores-formulario p { margin: 0 0 6px; }
        
        .errores-formulario p:last-child { margin-bottom: 0; }
    
    </style>

    <h1>Editar cliente</h1>

    <form class="formulario-cliente" method="POST" action="/clientes/{{ $cliente->id }}">
        @csrf
        @method('PUT')

        @if ($errors->any())
            <div class="errores-formulario">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <fieldset class="grupo-cliente">
            <legend>Información del cliente</legend>

            <div class="campo-cliente">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $cliente->nombre) }}" required>
            </div>

            <div class="campo-cliente">
                <label for="apellido">Apellido</label>
                <input type="text" id="apellido" name="apellido" value="{{ old('apellido', $cliente->apellido) }}" required>
            </div>

            <div class="campo-cliente">
                <label for="telefono">Teléfono</label>
                <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $cliente->telefono) }}" required>
            </div>

            <div class="campo-cliente">
                <label for="email">Email (opcional)</label>
                <input type="email" id="email" name="email" value="{{ old('email', $cliente->email) }}">
            </div>

            <div class="campo-cliente">
                <label for="cuit">CUIT (opcional)</label>
                <input type="text" id="cuit" name="cuit" value="{{ old('cuit', $cliente->cuit) }}">
            </div>

            <div class="campo-cliente">
                <label for="condicion_iva">Condición frente al IVA</label>
                <select id="condicion_iva" name="condicion_iva">
                    @foreach (\App\Models\Cliente::CONDICIONES_IVA as $condicion)
                        <option value="{{ $condicion }}" {{ old('condicion_iva', $cliente->condicion_iva) === $condicion ? 'selected' : '' }}>{{ $condicion }}</option>
                    @endforeach
                </select>
            </div>
        </fieldset>

        <button type="submit" class="boton-cliente">Guardar cambios</button>
        <a href="/clientes" class="boton-cliente">Cancelar</a>
    </form>

@endsection