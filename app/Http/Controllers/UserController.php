<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $middleware = [
        'role:reviewer' => ['only' => ['index']],
        'role:admin' => ['only' => ['index', 'store', 'update', 'destroy']],
    ];

    protected $userValidations = [];

    protected $translatedValidations = [];

    public function __construct()
    {
        $this->userValidations = [
            'name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required'],
        ];

        $this->translatedValidations = [
            'name.required' => 'El nombre es obligatorio',
            'name.string' => 'El nombre debe ser una cadena de caracteres',
            'name.max' => 'El nombre no debe superar los 100 caracteres',
    
            'last_name.string' => 'El apellido debe ser una cadena de caracteres',
            'last_name.max' => 'El apellido no debe superar los 100 caracteres',
    
            'email.required' => 'El email es obligatorio',
            'email.string' => 'El email debe ser una cadena de caracteres',
            'email.email' => 'El email debe ser un correo electrónico válido',
            'email.max' => 'El email no debe superar los 100 caracteres',
            'email.unique' => 'El email ya se encuentra en uso',

            'password.required' => 'La contraseña es obligatoria',
            'password.confirmed' => 'La confirmación de la contraseña no coincide',
    
            'role.required' => 'Debe elegir un rol',
        ];
    }

    /**
     * Devolver una lista de todos los usuarios.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with('roles')->get();
        return response()->json(['users' => $users], 200);
    }

    /**
     * Devolver el usuario autenticado actual.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAuthenticatedUser() {
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
        $request->validate($this->userValidations, $this->translatedValidations);

        try {

            $user = User::create([
                'name' => $request->name,
                'last_name' => $request->last_name ?? '',
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
    
            $user->assignRole($request->role);

            return response()->json(['message' => 'Usuario creado exitosamente'], 201);
        } catch (\Exception $e) {
            Log::error($e);

            return response()->json(['message' => 'Ocurrió un error al procesar la solicitud'], 500);
        }
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
        if (empty($request->password)) {
            $this->userValidations['password'] = ['nullable'];
        }

        $this->userValidations['email'] = ['required', 'string', 'email', 'max:100'];

        $request->validate($this->userValidations, $this->translatedValidations);

        try {
            $user = User::find($id);
            $user->name = $request->name;
            $user->last_name = $request->last_name ?? '';
            $user->email = $request->email;

            if ($request->password) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            $user->syncRoles($request->role);

            return response()->json(['message' => 'Usuario actualizado exitosamente'], 200);
        } catch (\Exception $e) {
            Log::error($e);

            return response()->json(['message' => 'Ocurrió un error al procesar la solicitud'], 500);
        }
    }

    /**
     * Eliminar el recurso especificado del almacenamiento.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $user = User::find($id);

            $user->delete();

            return response()->json(['message' => 'Usuario eliminado exitosamente'], 200);
        } catch (\Exception $e) {
            Log::error($e);

            return response()->json(['message' => 'Ocurrió un error al procesar la solicitud'], 500);
        }
    }
}
