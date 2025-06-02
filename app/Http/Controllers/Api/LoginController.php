<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
public function login(LoginRequest $request)
{
    try {
        // Busca o usuário pelo e-mail
        $user = User::where('email', $request->email)->first();

        // Se não encontrou o usuário ou senha inválida
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciais inválidas.'
            ], 401);
        }

        // Se o e-mail não está verificado
        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Você precisa verificar seu e-mail antes de fazer login.'
            ], 403);
        }

        $token = $user->createToken($user->email)->plainTextToken;
        return compact('token');
    } catch (Exception $error) {
        $this->errorHandler('Erro ao realizar login', $error, 401);
    }
}

    public function logout(Request $request)
    {
        try{
            $request->user()->tokens()->delete();
            return response()->json(['message' => 'Logout realizado com sucesso']);
        }catch(Exception $error){
            $this->errorHandler('Erro ao realizar logout', $error, 401);
        }
    }
}
