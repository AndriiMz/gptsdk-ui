<?php

namespace Tests\Helper;

use Gptsdk\AI\OpenAIVendor;
use Illuminate\Foundation\Testing\Concerns\InteractsWithContainer;
use Mockery\MockInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\Response\ResponseStream;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

trait WithAIMock
{
    use InteractsWithContainer;

    protected function mockAI()
    {
        $responseMock = \Mockery::mock(
            ResponseInterface::class,
            [
                'toArray' => [
                    ['content' => 'Hello Dude!']
                ],
                'getStatusCode' => 200
            ]
        );


        $this->mock(
            HttpClientInterface::class,
            function (MockInterface $mock) use ($responseMock) {
                $mock->shouldReceive('request')->andReturn(
                    $responseMock
                );
            }
        );
    }
}
