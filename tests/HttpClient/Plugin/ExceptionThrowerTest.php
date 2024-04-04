<?php

declare(strict_types=1);

/*
 * This file is part of the DigitalOcean API library.
 *
 * (c) Antoine Kirk <contact@sbin.dk>
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DigitalOceanV2\Tests\HttpClient\Plugin;

use DigitalOceanV2\Entity\RateLimit;
use DigitalOceanV2\Exception\ApiLimitExceededException;
use DigitalOceanV2\HttpClient\Plugin\ExceptionThrower;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Http\Client\Promise\HttpFulfilledPromise;
use PHPUnit\Framework\TestCase;

/**
 * @author Jon Cram <webigntion@gmail.com>
 */
class ExceptionThrowerTest extends TestCase
{
    public function testApiLimitExceededExceptionIncludesRateLimit(): void
    {
        $exceptionThrower = new ExceptionThrower();

        $rateLimitReset = rand();
        $rateLimitRemaining = rand();
        $rateLimitLimit = rand();

        $request = new Request('GET', 'https://example.com/');
        $response = new Response(
            429,
            [
                'RateLimit-Reset' => $rateLimitReset,
                'RateLimit-Remaining' => $rateLimitRemaining,
                'RateLimit-Limit' => $rateLimitLimit,
            ]
        );

        $callable = function () use ($response) {
            return new HttpFulfilledPromise($response);
        };

        $exception = null;

        try {
            $exceptionThrower->handleRequest($request, $callable, $callable);
        } catch (ApiLimitExceededException $exception) {
        }

        self::assertInstanceOf(ApiLimitExceededException::class, $exception);

        $expectedExceptionRateLimit = new RateLimit([
            'reset' => $rateLimitReset,
            'remaining' => $rateLimitRemaining,
            'limit' => $rateLimitLimit
        ]);

        self::assertEquals($expectedExceptionRateLimit, $exception->rateLimit);
    }
}
