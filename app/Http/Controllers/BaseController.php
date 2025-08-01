<?php

namespace App\Http\Controllers;

use App\Application\Services\ExceptionHandlerService;
use Illuminate\Http\JsonResponse;

abstract class BaseController extends Controller
{
    protected ExceptionHandlerService $exceptionHandler;

    public function __construct(ExceptionHandlerService $exceptionHandler)
    {
        $this->exceptionHandler = $exceptionHandler;
    }

    protected function handleRequest(callable $callback): JsonResponse
    {
        return $this->exceptionHandler->handle($callback);
    }
} 
