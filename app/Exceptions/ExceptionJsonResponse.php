<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;


class ExceptionJsonResponse extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        $previous = $this->getPrevious();
        $statusHttp = $this->getCode() ?? 500;

        $responseError = [
            'message' => $this->getMessage(),
        ];
        if(env('APP_DEBUG'))
            $responseError = [
                ...$responseError,
                'exception' => $previous->getMessage(),
                'error' => $previous,
                'trace' => $previous->getTrace()
            ];
            return response()
                ->json($responseError)
                ->setStatusCode($statusHttp, $this->getMessage());
    }
}
