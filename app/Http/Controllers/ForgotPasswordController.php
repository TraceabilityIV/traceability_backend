<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPassword\ResetPasswordRequest;
use App\Http\Requests\ForgotPassword\SendLinkRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function sendResetLink(SendLinkRequest $request)
    {
		try {
			$status = Password::sendResetLink(
				$request->only('email')
			);

			Log::info($status);
	
			return $status === Password::RESET_LINK_SENT
				? response()->json(['message' => __($status)], 200)
				: response()->json(['message' => __($status)], 400);
		} catch (\Throwable $th) {
			return response()->json([
				"error" => "Error",
				"mensaje" => "Error en el servidor",
			], 500);

			Log::error($th);
		}
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->save();
            }
        );

        // Si es una solicitud AJAX/API
        if ($request->wantsJson()) {
            return $status === Password::PASSWORD_RESET
                ? response()->json(['message' => __($status)], 200)
                : response()->json(['message' => __($status)], 400);
        }

        // Para solicitudes web
        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('password.success');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }

    /**
     * Muestra la vista de éxito después de restablecer la contraseña
     */
    public function showSuccessPage()
    {
        return view('auth.password-reset-success');
    }
}
