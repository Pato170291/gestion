<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Services\CuentaCorrienteService;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index(Request $request, CuentaCorrienteService $cuentaCorriente)
    {
        $buscar = $request->input('buscar');

        if ($buscar) {
            $proveedores = Proveedor::where('empresa', 'like', "%$buscar%")
                ->orWhere('contacto', 'like', "%$buscar%")
                ->orWhere('telefono', 'like', "%$buscar%")
                ->orWhere('email', 'like', "%$buscar%")
                ->orWhere('cuit', 'like', "%$buscar%")
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->paginate(6);
        } else {
            $proveedores = Proveedor::orderByDesc('updated_at')
                ->orderByDesc('id')
                ->paginate(6);
        }

        $proveedores->appends(['buscar' => $buscar]);

        foreach ($proveedores as $proveedor) {
            $proveedor->saldo = $cuentaCorriente->saldoProveedor($proveedor);
        }

        if ($request->ajax()) {
            return view('partials.proveedores-ajax', compact('proveedores'));
        }

        return view('proveedores', compact('proveedores'));
    }
          

    public function crear()
    {
        return view('crear-proveedor');
    }

    public function store(Request $request)
    {
        $request->validate([
            'empresa' => 'required|max:255',
            'contacto' => 'nullable|max:255',
            'telefono' => 'required|numeric',
            'email' => 'nullable|email',
            'direccion' => 'nullable|max:255',
            'cuit' => ['nullable', 'max:20', 'regex:/^[0-9]+$/'],
            'condicion_iva' => 'nullable|in:' . implode(',', Proveedor::CONDICIONES_IVA),
            'activo' => 'nullable|boolean',
        ], [
            'empresa.required' => 'La empresa es obligatoria.',
            'empresa.max' => 'La empresa no puede superar los 255 caracteres.',

            'contacto.max' => 'El contacto no puede superar los 255 caracteres.',

            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.numeric' => 'El teléfono debe contener solamente números.',

            'email.email' => 'El email no tiene un formato válido.',

            'direccion.max' => 'La dirección no puede superar los 255 caracteres.',

            'cuit.max' => 'El CUIT no puede superar los 20 caracteres.',
            'cuit.regex' => 'El CUIT debe contener solamente números, sin guiones ni espacios.',

            'condicion_iva.in' => 'La condición frente al IVA seleccionada no es válida.',

            'activo.required' => 'El estado es obligatorio.',
            'activo.boolean' => 'El estado seleccionado no es válido.',
        ]);

        $proveedor = new Proveedor();

        $proveedor->empresa = $request->input('empresa');
        $proveedor->contacto = $request->input('contacto');
        $proveedor->telefono = $request->input('telefono');
        $proveedor->email = $request->input('email');
        $proveedor->direccion = $request->input('direccion');
        $proveedor->cuit = $request->input('cuit');
        $proveedor->condicion_iva = $request->input('condicion_iva') ?: 'Consumidor Final';
        $proveedor->activo = $request->input('activo', true);

        $proveedor->save();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Proveedor cargado con éxito.',
                'proveedor' => $proveedor,
            ]);
        }

        return redirect('/proveedores')->with('success', 'Proveedor creado exitosamente');
    }

    public function edit($id)
    {
        $proveedor = Proveedor::find($id);
        return view('editar-proveedor', compact('proveedor'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'empresa' => 'required|max:255',
            'contacto' => 'nullable|max:255',
            'telefono' => 'required|numeric',
            'email' => 'nullable|email',
            'direccion' => 'nullable|max:255',
            'cuit' => ['nullable', 'max:20', 'regex:/^[0-9]+$/'],
            'condicion_iva' => 'nullable|in:' . implode(',', Proveedor::CONDICIONES_IVA),
            'activo' => 'required|boolean',
        ], 
        
        [
            'empresa.required' => 'La empresa es obligatoria.',
            'empresa.max' => 'La empresa no puede superar los 255 caracteres.',

            'contacto.max' => 'El contacto no puede superar los 255 caracteres.',

            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.numeric' => 'El teléfono debe contener solamente números.',

            'email.email' => 'El email no tiene un formato válido.',

            'direccion.max' => 'La dirección no puede superar los 255 caracteres.',

            'cuit.max' => 'El CUIT no puede superar los 20 caracteres.',
            'cuit.regex' => 'El CUIT debe contener solamente números, sin guiones ni espacios.',

            'condicion_iva.in' => 'La condición frente al IVA seleccionada no es válida.',

            'activo.required' => 'El estado es obligatorio.',
            'activo.boolean' => 'El estado seleccionado no es válido.',
        ]);

        $proveedor = Proveedor::find($id);

        $proveedor->empresa = $request->input('empresa');
        $proveedor->contacto = $request->input('contacto');
        $proveedor->telefono = $request->input('telefono');
        $proveedor->email = $request->input('email');
        $proveedor->direccion = $request->input('direccion');
        $proveedor->cuit = $request->input('cuit');
        $proveedor->condicion_iva = $request->input('condicion_iva') ?: 'Consumidor Final';
        $proveedor->activo = $request->input('activo');

        $proveedor->save();

        return redirect('/proveedores')->with('success', 'Proveedor actualizado exitosamente');
    }

    public function cambiarEstado($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        $proveedor->activo = !$proveedor->activo;
        $proveedor->save();

        return redirect('/proveedores')->with('success', $proveedor->activo
            ? 'Proveedor activado correctamente.'
            : 'Proveedor desactivado correctamente.');
    }

    public function cuentaCorriente($id, CuentaCorrienteService $cuentaCorriente)
    {
        $proveedor = Proveedor::findOrFail($id);
        $resumen = $cuentaCorriente->resumenProveedor($proveedor);

        return response()->json([
            'id' => $proveedor->id,
            'proveedor' => $proveedor->empresa,
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

        $cuentaCorriente->registrarPagoProveedor(
            Proveedor::findOrFail($id),
            (float) $validated['monto'],
            $validated['medio'],
            $validated['observacion'] ?? null
        );

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'El pago al proveedor fue registrado correctamente.']);
        }

        return redirect('/proveedores')->with('success', 'El pago al proveedor fue registrado correctamente.');
    }
}
