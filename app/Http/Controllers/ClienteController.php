<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Services\CuentaCorrienteService;

class ClienteController extends Controller
{
    public function index(Request $request, CuentaCorrienteService $cuentaCorriente)
    {
        $buscar = $request->input('buscar');

      if ($buscar) {
            $clientes = Cliente::where('nombre', 'like', "%$buscar%")
                ->orWhere('apellido', 'like', "%$buscar%")
                ->orWhere('telefono', 'like', "%$buscar%")
                ->orWhere('email', 'like', "%$buscar%")
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->paginate(6);
        } else {
            $clientes = Cliente::orderByDesc('updated_at')
                ->orderByDesc('id')
                ->paginate(6);
        }

        $clientes->appends(['buscar' => $buscar]);

        foreach ($clientes as $cliente) {
            $cliente->saldo = $cuentaCorriente->saldoCliente($cliente);
        }

        if($request->ajax()) {
            return view('partials.clientes-ajax', compact('clientes'));
        }

        return view('clientes', compact('clientes'));
    }

    public function crear()
    {
        return view('crear-cliente');
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'telefono' => 'required|numeric',
                'email' => 'nullable|email|unique:clientes,email',
                'cuit' => 'nullable|string|max:20',
                'condicion_iva' => 'nullable|in:' . implode(',', Cliente::CONDICIONES_IVA),
            ],
            
            [
                'nombre.required' => 'El campo nombre es obligatorio.',
                'apellido.required' => 'El campo apellido es obligatorio.',
                'telefono.required' => 'El campo teléfono es obligatorio.',
                'telefono.numeric' => 'El campo teléfono debe ser un número.',
                'telefono.max' => 'El campo teléfono no puede tener más de 30 dígitos.',
                'email.email' => 'El campo correo electrónico debe ser una dirección de correo válida.',
                'email.unique' => 'El correo electrónico ya está en uso.',
                'condicion_iva.in' => 'La condición frente al IVA seleccionada no es válida.',
            ]
        );
    
        $cliente = new Cliente();
        $cliente->nombre = $request->input('nombre');
        $cliente->apellido = $request->input('apellido');
        $cliente->telefono = $request->input('telefono');
        $cliente->email = $request->input('email');
        $cliente->cuit = $request->input('cuit');
        $cliente->condicion_iva = $request->input('condicion_iva') ?: 'Consumidor Final';
        $cliente->activo = true;
        $cliente->save();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Cliente cargado con éxito.',
                'cliente' => $cliente,
            ]);
        }

        return redirect('/clientes') ->with('success', 'Cliente creado exitosamente.');
    }

    public function cambiarEstado($id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->activo = !$cliente->activo;
        $cliente->save();

        return redirect('/clientes')->with('success', $cliente->activo
            ? 'Cliente activado correctamente.'
            : 'Cliente desactivado correctamente.');
    }

    public function edit($id)
    {
        $cliente = Cliente::findOrFail($id);
        return view('editar-cliente', compact('cliente'));
    }

    public function update(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);

        $request->validate([
            'nombre' => 'required|regex:/^[\pL\s]+$/u|max:255',
            'apellido' => 'required|regex:/^[\pL\s]+$/u|max:255',
            'telefono' => 'required|numeric',
            'email' => 'nullable|email|unique:clientes,email,' . $id,
            'cuit' => 'nullable|string|max:20',
            'condicion_iva' => 'nullable|in:' . implode(',', Cliente::CONDICIONES_IVA),
        ]);

        $cliente->nombre = $request->input('nombre');
        $cliente->apellido = $request->input('apellido');
        $cliente->telefono = $request->input('telefono');
        $cliente->email = $request->input('email');
        $cliente->cuit = $request->input('cuit');
        $cliente->condicion_iva = $request->input('condicion_iva') ?: 'Consumidor Final';

        $cliente->save();

        return redirect('/clientes')->with('success', 'Cliente actualizado correctamente');
    }

    public function cuentaCorriente($id, CuentaCorrienteService $cuentaCorriente)
    {
        $cliente = Cliente::findOrFail($id);
        $resumen = $cuentaCorriente->resumenCliente($cliente);

        return response()->json([
            'id' => $cliente->id,
            'cliente' => $cliente->nombre . ' ' . $cliente->apellido,
            'nombre' => $cliente->nombre,
            'apellido' => $cliente->apellido,
            'cuit' => $cliente->cuit,
            'telefono' => $cliente->telefono,
            'email' => $cliente->email,
            'condicion_iva' => $cliente->condicion_iva,
            'saldo' => $resumen['saldo'],
            'movimientos' => array_slice($resumen['movimientos'], 0, 5),
        ]);
    }

    public function registrarPago(Request $request, $id, CuentaCorrienteService $cuentaCorriente)
    {
        $validated = $request->validate([
            'monto' => 'required|numeric|gt:0',
            'medio' => 'required|in:efectivo,tarjeta,transferencia',
            'observacion' => 'nullable|string',
        ]);

        $cuentaCorriente->registrarPagoCliente(
            Cliente::findOrFail($id),
            (float) $validated['monto'],
            $validated['medio'],
            $validated['observacion'] ?? null
        );

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'El pago del cliente fue registrado correctamente.']);
        }

        return redirect('/clientes')->with('success', 'El pago del cliente fue registrado correctamente.');
    }
}
