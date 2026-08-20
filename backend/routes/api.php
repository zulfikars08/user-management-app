<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Authentication and authorization are intentionally added in Step 5.
Route::apiResource('users', UserController::class);
