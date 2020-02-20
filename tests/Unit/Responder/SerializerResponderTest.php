<?php declare(strict_types=1);

namespace App\Tests\Unit\Responder;

use App\Responder\SerializerResponder;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class SerializerResponderTest extends TestCase
{
    private SerializerResponder $subject;

    /** @var SerializerInterface|MockObject */
    private MockObject $serializerMock;

    protected function setUp(): void
    {
        $this->serializerMock = $this->createMock(SerializerInterface::class);
    }

    public function testCreateJsonResponse(): void
    {
        $this->initSerializerResponder();

        $data = ['foo' => 'bar'];

        $this->serializerMock
            ->expects($this->once())
            ->method('serialize')
            ->with($data, 'json', [])
            ->willReturn('serializedData');

        $response = $this->subject->createJsonResponse($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('serializedData', $response->getContent());
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testCreateJsonResponseWithStatusCode(): void
    {
        $this->initSerializerResponder();

        $data = ['foo' => 'bar'];

        $this->serializerMock
            ->expects($this->once())
            ->method('serialize')
            ->with($data, 'json', [])
            ->willReturn('serializedData');

        $response = $this->subject->createJsonResponse($data, Response::HTTP_ACCEPTED);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('serializedData', $response->getContent());
        $this->assertSame(Response::HTTP_ACCEPTED, $response->getStatusCode());
    }

    public function testCreateJsonResponseWithHeaders(): void
    {
        $this->initSerializerResponder();

        $data = ['foo' => 'bar'];

        $this->serializerMock
            ->expects($this->once())
            ->method('serialize')
            ->with($data, 'json', [])
            ->willReturn('serializedData');

        $response = $this->subject->createJsonResponse($data, Response::HTTP_OK, ['foo' => 'bar']);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('serializedData', $response->getContent());
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSame('bar', $response->headers->get('foo'));
    }

    public function testCreateJsonResponseWithSerializerContext(): void
    {
        $this->initSerializerResponder();

        $data = ['foo' => 'bar'];

        $this->serializerMock
            ->expects($this->once())
            ->method('serialize')
            ->with($data, 'json', ['foo' => 'bar'])
            ->willReturn('serializedData');

        $response = $this->subject->createJsonResponse($data, Response::HTTP_OK, [], ['foo' => 'bar']);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('serializedData', $response->getContent());
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testCreateErrorJsonResponse(): void
    {
        $this->initSerializerResponder();

        $this->serializerMock
            ->expects($this->once())
            ->method('serialize')
            ->with([
                'error' => [
                    'message' => 'foo',
                ],
            ], 'json', [])
            ->willReturn('serializedData');

        $exception = new Exception('foo');

        $response = $this->subject->createErrorJsonResponse($exception);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('serializedData', $response->getContent());
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testCreateErrorJsonResponseWithTrace(): void
    {
        $this->initSerializerResponder(true);

        $this->serializerMock
            ->expects($this->once())
            ->method('serialize')
            ->with($this->callback(static function ($array) {
                return array_key_exists('error', $array)
                    && array_key_exists('message', $array['error'])
                    && array_key_exists('trace', $array['error']);
            }), 'json', [])
            ->willReturn('serializedData');

        $exception = new Exception('foo');

        $response = $this->subject->createErrorJsonResponse($exception);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('serializedData', $response->getContent());
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testCreateErrorJsonResponseWithHttpException(): void
    {
        $this->initSerializerResponder();

        $this->serializerMock
            ->expects($this->once())
            ->method('serialize')
            ->with([
                'error' => [
                    'message' => '',
                ],
            ], 'json', [])
            ->willReturn('serializedData');

        $exception = new TooManyRequestsHttpException('foo');

        $response = $this->subject->createErrorJsonResponse($exception);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('serializedData', $response->getContent());
        $this->assertSame(Response::HTTP_TOO_MANY_REQUESTS, $response->getStatusCode());
        $this->assertSame('foo', $response->headers->get('Retry-After'));
    }

    public function testCreateErrorJsonResponseWithStatusCode(): void
    {
        $this->initSerializerResponder();

        $this->serializerMock
            ->expects($this->once())
            ->method('serialize')
            ->with([
                'error' => [
                    'message' => 'foo',
                ],
            ], 'json', [])
            ->willReturn('serializedData');

        $exception = new Exception('foo');

        $response = $this->subject->createErrorJsonResponse($exception, Response::HTTP_GATEWAY_TIMEOUT);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('serializedData', $response->getContent());
        $this->assertSame(Response::HTTP_GATEWAY_TIMEOUT, $response->getStatusCode());
    }

    public function testCreateErrorJsonResponseWithHeaders(): void
    {
        $this->initSerializerResponder();

        $this->serializerMock
            ->expects($this->once())
            ->method('serialize')
            ->with([
                'error' => [
                    'message' => 'foo',
                ],
            ], 'json', [])
            ->willReturn('serializedData');

        $exception = new Exception('foo');

        $response = $this->subject->createErrorJsonResponse(
            $exception,
            Response::HTTP_INTERNAL_SERVER_ERROR,
            ['foo' => 'bar']
        );

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('serializedData', $response->getContent());
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertSame('bar', $response->headers->get('foo'));
    }

    public function testCreateErrorJsonResponseWithSerializerContext(): void
    {
        $this->initSerializerResponder();

        $this->serializerMock
            ->expects($this->once())
            ->method('serialize')
            ->with([
                'error' => [
                    'message' => 'foo',
                ],
            ], 'json', ['foo' => 'bar'])
            ->willReturn('serializedData');

        $exception = new Exception('foo');

        $response = $this->subject->createErrorJsonResponse(
            $exception,
            Response::HTTP_INTERNAL_SERVER_ERROR,
            [],
            ['foo' => 'bar']
        );

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('serializedData', $response->getContent());
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    private function initSerializerResponder($isDebugEnabled = false): void
    {
        $this->subject = new SerializerResponder($this->serializerMock, $isDebugEnabled);
    }
}
