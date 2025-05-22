<?php

use App\Http\Controllers\ForgotPasswordController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Ruta para mostrar el formulario de restablecimiento
Route::get('/reset-password/{token}/{email}', function ($token, $email) {
    // Buscar el token en la base de datos
    $record = DB::table('password_reset_tokens')
        ->where('email', $email)
        ->first();

    // Verificar si el token existe y es válido
    $isValid = false;
    $error = null;

    if (!$record) {
        $error = 'El enlace de restablecimiento no es válido o ha expirado.';
    } elseif (!Hash::check($token, $record->token)) {
        $error = 'El token de restablecimiento no es válido.';
    } elseif (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
        $error = 'El enlace de restablecimiento ha expirado.';
    } else {
        $isValid = true;
    }

    return view('auth.reset-password', [
        'token' => $token,
        'email' => $email,
        'isValid' => $isValid,
        'error' => $error
    ]);
})->name('password.reset');

// Ruta para mostrar el mensaje de éxito después del restablecimiento
Route::get('/password/reset/success', [ForgotPasswordController::class, 'showSuccessPage'])
    ->name('password.success');
