<?php
namespace App\Application\Services;

use App\Exceptions\CustomException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;
use TypeError;
use Error;

class ExceptionHandlerService
{
    public function __construct() {
    }

    public function handle(callable $callback): JsonResponse {
        try {
            return $callback();
        } catch (CustomException $e) {
            return $e->render();
        } catch (TypeError $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Tipo de dado inválido.'], 400);
        } catch (Error $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Erro inesperado.'], 500);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Erro interno.'], 500);
        }
    }
}