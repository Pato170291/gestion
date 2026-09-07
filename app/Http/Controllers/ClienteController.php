<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

      if ($buscar) {
            $clientes = Cliente::where('nombre', 'like', "%$buscar%")
                ->orWhere('apellido', 'like', "%$buscar%")
                ->orWhere('telefono', 'like', "%$buscar%")
                ->orWhere('email', 'like', "%$buscar%")
                ->paginate(6);
        } else {
            $clientes = Cliente::paginate(6);
        }

        $clientes->appends(['buscar' => $buscar]);
        
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
                'email' => 'required|email|unique:clientes,email',
            ],
            
            [
                'nombre.required' => 'El campo nombre es obligatorio.',
                'apellido.required' => 'El campo apellido es obligatorio.',
                'telefono.required' => 'El campo teléfono es obligatorio.',
                'telefono.numeric' => 'El campo teléfono debe ser un número.',
                'telefono.max' => 'El campo teléfono no puede tener más de 30 dígitos.',
                'email.required' => 'El campo correo electrónico es obligatorio.',
                'email.email' => 'El campo correo electrónico debe ser una dirección de correo válida.',
                'email.unique' => 'El correo electrónico ya está en uso.',
            ]
        );
    
        $cliente = new Cliente();
        $cliente->nombre = $request->input('nombre');
        $cliente->apellido = $request->input('apellido');
        $cliente->telefono = $request->input('telefono');
        $cliente->email = $request->input('email');
        $cliente->save();

        return redirect('/clientes') ->with('success', 'Cliente creado exitosamente.');
    }

    public function destroy($id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->delete();

        return redirect('/clientes')->with('success', 'Cliente eliminado exitosamente.');
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
            'email' => 'required|email|unique:clientes,email,' . $id,
        ]);

        $cliente->nombre = $request->input('nombre');
        $cliente->apellido = $request->input('apellido');
        $cliente->telefono = $request->input('telefono');
        $cliente->email = $request->input('email');

        $cliente->save();

        return redirect('/clientes')->with('success', 'Cliente actualizado correctamente');
    }
}
