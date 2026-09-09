<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index(Request $request)
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
            'activo' => 'required|boolean',
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
        $proveedor->activo = $request->input('activo');

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
        $proveedor->activo = $request->input('activo');

        $proveedor->save();

        return redirect('/proveedores')->with('success', 'Proveedor actualizado exitosamente');
    }

    public function destroy($id)
    {
        $proveedor = Proveedor::find($id);
        $proveedor->delete();

        return redirect('/proveedores')->with('success', 'Proveedor eliminado exitosamente');
    }
}
