<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;

class Controller extends BaseController {
    use AuthorizesRequests, DispatchesJobs;

    protected function respond($data, $status = 200): JsonResponse {
        return response()->json($data, $status);
    }
}