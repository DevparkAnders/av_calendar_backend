<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Role;

class RoleController extends Controller
{
    /**
     * Get list of all roles
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return ApiResponse::responseOk(Role::orderBy('id')->get());
    }
}
