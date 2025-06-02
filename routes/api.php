<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PartidaController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ReportMessageController;
use App\Http\Controllers\Api\LoginController;
use App\models\Partida;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware(['auth:sanctum', 'verified']);

Route::get('partidas/ranking', [PartidaController::class, 'ranking'])
    ->name('partidas.ranking');

    Route::middleware(['auth:sanctum', 'verified'])->post('/partidas/simular', [PartidaController::class, 'simularPartida']);

    Route::middleware(['auth:sanctum', 'verified'])->get('/partidas/user', [PartidaController::class, 'showByUser']);

Route::apiResource('partidas', PartidaController::class)
    ->middleware(['auth:sanctum', 'verified']);

    Route::apiResource('partidas', PartidaController::class)
    ->only(['index', 'show']);

Route::apiResource('users', UserController::class)
->middleware(['auth:sanctum', 'verified']);

Route::apiResource('users', UserController::class)
->only(['index', 'show', 'store']);

Route::get('/jogadores', [UserController::class, 'listarJogadores']);

Route::middleware(['auth:sanctum', 'verified'])->get('/denuncias/user', [DenunciaController::class, 'showByUser']);




Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('reports', ReportController::class);
    Route::get('reports/{report}/messages', [ReportMessageController::class, 'index']);
    Route::post('reports/{report}/messages', [ReportMessageController::class, 'store']);
});

Route::middleware(['auth:sanctum', 'verified'])->patch('/reports/{report}/status', [ReportController::class, 'updateStatus']);


//Route::get('/ranking', [PartidaController::class, 'ranking']);
// Route::post('/partida' , [PartidaController::class, 'store']);
// Route::post('/denuncia' , [DenunciaController::class, 'store']);
// Route::post('/user' , [UserController::class, 'store']);

Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])
->middleware(['auth:sanctum', 'verified']);


Route::get('/email/verify/{id}/{hash}', function (Request $request) {
    $user = User::findOrFail($request->id);

    if (! hash_equals((string) $request->hash, sha1($user->getEmailForVerification()))) {
        throw new AuthorizationException;
    }

    if (!$user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
    }

    return redirect('http://localhost:5173/email-verificado'); // rota para o front
})->name('verification.verify');

Route::post('/email/resend-public', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['message' => 'Usuário não encontrado.'], 404);
    }

    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'E-mail já verificado.'], 400);
    }

    $user->sendEmailVerificationNotification();

    return response()->json(['message' => 'E-mail de verificação reenviado!']);
});




Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);
    Password::sendResetLink($request->only('email'));
    return response()->json(['message' => 'Verifique seu email. Instruções para redefinir a senha foram enviadas pelo nosso email blackjacktcc@gmail.com.']);
});


Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|confirmed',
    ]);
    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => bcrypt($password)
            ])->save();
        }
    );
    // Adicione este log para depurar
    \Log::info('Password reset status:', ['status' => $status]);
    return $status === Password::PASSWORD_RESET
        ? response()->json(['message' => 'Senha redefinida com sucesso!'])
        : response()->json(['message' => 'Erro ao redefinir senha.', 'status' => $status], 400);
});
