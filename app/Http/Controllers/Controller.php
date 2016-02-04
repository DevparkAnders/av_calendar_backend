<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Helpers\ErrorCode;
use App\Services\PermissionService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var PermissionService
     */
    protected $permService;

    /**
     * Controller constructor.
     *
     * @param PermissionService $permService
     */
    public function __construct(PermissionService $permService)
    {
        $this->permService = $permService;
    }

    /**
     * Verifies whether user has all given permission(s). In case he does not,
     * return error response, otherwise return false
     *
     * @param string|array $permission
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    protected function cannot($permission)
    {
        $allowed = $this->permService->can(auth()->user(), $permission);

        if (!$allowed) {
            return ApiResponse::responseError(ErrorCode::NO_PERMISSION, 401);
        }

        return false;
    }
}
