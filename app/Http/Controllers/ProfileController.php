<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UserResource;
class ProfileController extends Controller
{
    public function me(): JsonResponse
    {
        return response()->json(new UserResource(auth()->user()));
    }
}
