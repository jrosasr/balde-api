<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $middleware = [
        'role:reviewer' => ['only' => ['index']],
        'role:admin' => ['only' => ['index', 'store', 'update', 'destroy']],
    ];

    /**
     * Devolver una lista de todos los usuarios.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return response()->json(['users' => $users], 200);
    }

    /**
     * Devolver el usuario autenticado actual.
     *
     * @return \Illuminate\Http\Response
     */
    public function userAuthenticated() {
        $user = User::with('roles')->find(auth()->user()->id);
        return response()->json(['user' => $user], 200);
    }

    /**
     * Devolver un solo usuario.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::with('roles')->find($id);
        return response()->json(['user' => $user], 200);
    }

    /**
     * Almacenar un recurso recién creado en el almacenamiento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required'],
        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.string' => 'El nombre debe ser una cadena de caracteres',
            'name.max' => 'El nombre no debe superar los 100 caracteres',

            'email.required' => 'El email es obligatorio',
            'email.string' => 'El email debe ser una cadena de caracteres',
            'email.email' => 'El email debe ser un correo electrónico válido',
            'email.max' => 'El email no debe superar los 100 caracteres',
            'email.unique' => 'El email ya se encuentra en uso',

            'password.required' => 'La contraseña es obligatoria',
            'password.confirmed' => 'Las contraseñas no coinciden',

            'role.required' => 'Debe elegir un rol',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return response()->json(['message' => 'Usuario creado exitosamente'], 201);
    }

    /**
     * Actualizar el recurso especificado en el almacenamiento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:'.User::class],
            'password' => ['confirmed', Rules\Password::defaults()],
            'role' => ['required'],
        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.string' => 'El nombre debe ser una cadena de caracteres',
            'name.max' => 'El nombre no debe superar los 100 caracteres',

            'email.required' => 'El email es obligatorio',
            'email.string' => 'El email debe ser una cadena de caracteres',
            'email.email' => 'El email debe ser un correo electrónico válido',
            'email.max' => 'El email no debe superar los 100 caracteres',
            'email.unique' => 'El email ya se encuentra en uso',

            'password.confirmed' => 'Las contraseñas no coinciden',

            'role.required' => 'Debe elegir un rol',
        ]);

        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        $user->syncRoles($request->role);

        return response()->json(['message' => 'Usuario actualizado exitosamente'], 200);
    }

    /**
     * Eliminar el recurso especificado del almacenamiento.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        return response()->json(['message' => 'Usuario eliminado exitosamente'], 200);
    }
}
