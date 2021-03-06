<?php

declare(strict_types=1);

namespace Tracking3\Core\ClientTest\Token;

use JsonException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tracking3\Core\Client\Configuration;
use Tracking3\Core\Client\EnvironmentHandlingService;
use Tracking3\Core\Client\Http\Http;
use Tracking3\Core\Client\Token\AccessTokenRequest;
use Tracking3\Core\ClientTest\ReflectionTrait;

class AccessTokenRequestTest extends TestCase
{

    use ReflectionTrait;

    /**
     * @throws JsonException
     */
    public function testGetAccessToken(): void
    {
        $configuration = new Configuration(
            [
                'email' => 'john@example.com',
                'password' => 's3cr37',
            ]
        );

        /** @var AccessTokenRequest|MockObject $requestMock */
        $requestMock = $this->getMockBuilder(AccessTokenRequest::class)
            ->setConstructorArgs(
                [
                    $configuration,
                ]
            )
            ->setMethodsExcept(['get'])
            ->getMock();

        $httpMock = $this->getMockBuilder(Http::class)
            ->disableOriginalConstructor()
            ->getMock();

        $httpMock->expects(self::once())
            ->method('get')
            ->with(
                implode(
                    '/',
                    [
                        EnvironmentHandlingService::API_URI_ENV_PRODUCTION,
                        EnvironmentHandlingService::API_VERSION,
                        'token',
                        'access',
                    ]
                )
            )
            ->willReturn(['payload' => ['jwt' => 'json.web.token']]);

        $requestMock->method('getHttp')
            ->willReturn($httpMock);

        self::assertEquals('json.web.token', $requestMock->get());
    }
}
