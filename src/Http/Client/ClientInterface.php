<?php

namespace Hoo\WordPressPluginFramework\Http\Client;

use Hoo\WordPressPluginFramework\{
	Http\Client\Request\RequestInterface,
	Http\Client\Response\ResponseInterface,
};

interface ClientInterface
{
	public function get(string $url, ?array $headers = null, array|string|null $body = null): ResponseInterface;
	public function head(string $url, ?array $headers = null, array|string|null $body = null): ResponseInterface;
	public function post(string $url, ?array $headers = null, array|string|null $body = null): ResponseInterface;
	public function put(string $url, ?array $headers = null, array|string|null $body = null): ResponseInterface;
	public function patch(string $url, ?array $headers = null, array|string|null $body = null): ResponseInterface;
	public function delete(string $url, ?array $headers = null, array|string|null $body = null): ResponseInterface;
	public function options(string $url, ?array $headers = null, array|string|null $body = null): ResponseInterface;

	public function request(RequestInterface $request): ResponseInterface;
}
