<?php declare(strict_types=1);

namespace App\Responder;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

class SerializerResponder
{
    private SerializerInterface $serializer;
    private bool $isDebugEnabled;

    public function __construct(SerializerInterface $serializer, bool $isDebugEnabled = false)
    {
        $this->serializer = $serializer;
        $this->isDebugEnabled = $isDebugEnabled;
    }

    public function createJsonResponse(
        $data,
        int $statusCode = Response::HTTP_OK,
        array $headers = [],
        array $serializerContext = []
    ): JsonResponse {
        return JsonResponse::fromJsonString(
            $this->serializer->serialize($data, 'json', $serializerContext),
            $statusCode,
            $headers
        );
    }

    public function createErrorJsonResponse(
        Throwable $exception,
        int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR,
        array $headers = [],
        array $serializerContext = []
    ): JsonResponse {
        $content = ['message' => $exception->getMessage()];

        if ($this->isDebugEnabled) {
            $content['trace'] = $exception->getTraceAsString();
        }

        return JsonResponse::fromJsonString(
            $this->serializer->serialize(['error' => $content], 'json', $serializerContext),
            $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : $statusCode,
            $exception instanceof HttpExceptionInterface ? $exception->getHeaders() : $headers
        );
    }
}
