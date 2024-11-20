<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        $fields = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            
            'email.required' => "L'adresse e-mail est obligatoire.",
            'email.email' => "L'adresse e-mail doit être une adresse valide.",
            'email.unique' => 'Cette adresse e-mail est déjà utilisée.',
            
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'Le mot de passe ne correspond pas.',
        ]);
        

        $user = User::create($fields);

        $token = $user -> createToken($request -> name);
        return [
            'user'=> $user,
            'token'=>$token -> plainTextToken
        ];
    }
    public function login(Request $request){
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required',
        ], [
            'email.required' => "L'adresse e-mail est obligatoire.",
            'email.email' => "L'adresse e-mail doit être une adresse valide.",
            'email.exists' => 'Aucun utilisateur trouvé avec cette adresse e-mail.',
            
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);
        

        $user = User::where('email' , $request -> email)->first();
        if(!$user || !Hash::check($request->password, $user->password)){
            return [
                'errors' => [
                    'email' => ['Les informations de connection sont incorrect !!']
                ]
            ]; 
        }

        $token = $user -> createToken($user -> name);
        return [
            'user'=> $user,
            'token'=>$token -> plainTextToken
        ];
        
    }
    public function logout(Request $request){
        $request -> user() -> tokens() -> delete();
        return [
            'message' => 'Tu as été déconnecté'
        ];
    }
}
