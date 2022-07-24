<?php


namespace App\Services\Responses;


use App\Enums\SuccessMessages;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ResponseService implements IResponseService
{
    public function __construct()
    {
    }

    public function successResponse(array $data = null, string $message = SuccessMessages::success): JsonResponse
    {
        return $this->success($message, Response::HTTP_OK, $data);
    }

    public function createdResponse(array $data = null, string $message = SuccessMessages::success): JsonResponse
    {
        return $this->success($message, Response::HTTP_CREATED, $data);
    }

    public function noContentResponse(string $message = SuccessMessages::recordDeleted): JsonResponse
    {
        return $this->success($message, Response::HTTP_OK);
    }

    public function notFound(string $message): JsonResponse
    {
        $response = [
            'message' => $message
        ];

        return response()->json($response, Response::HTTP_NOT_FOUND);
    }

    public function unavailable(string $message): JsonResponse
    {
        $response = [
            'message' => $message
        ];

        return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function success(string $message, int $statusCode, array $data = null): JsonResponse
    {
        $response = [
            'message' => $message,
        ];

        if ($data !== null) $response['data'] = $data;

        return response()->json($response, $statusCode);
    }

}
