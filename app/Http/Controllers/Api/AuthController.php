<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();
        if (!empty($user) && Hash::check($request->password, $user->getAuthPassword())) {
            if ($user->currentAccessToken() !== null) {
                $user->currentAccessToken()->delete();
            }
            return new JsonResponse([
                'token' => $user->createToken('authToken')->plainTextToken
            ]);
        }
        return new JsonResponse(['message' => 'Invalid Credentials'], 401);
    }
}
