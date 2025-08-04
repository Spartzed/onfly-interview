<?php

namespace App\Http\Controllers\Api;

use App\Application\Services\DashboardStatsService;
use App\Http\Controllers\BaseController;
use App\Application\Services\ExceptionHandlerService;
use Illuminate\Http\JsonResponse;

class DashboardStatsController extends BaseController
{
    public function __construct(
        ExceptionHandlerService $exceptionHandler,
        private DashboardStatsService $dashboardStatsService
    ) {
        parent::__construct($exceptionHandler);
    }

    public function index(): JsonResponse
    {
        return $this->handleRequest(function () {
            return $this->dashboardStatsService->getStats();
        });
    }
}
