<?php

namespace Hoo\WordPressPluginFramework\Http\Client;

use Hoo\WordPressPluginFramework\{
	Http\Client\Request\RequestInterface,
	Http\Client\Request\RequestFactoryInterface,
	Http\Client\Response\ResponseInterface,
	Http\Client\Response\ResponseFactoryInterface,
};
use WP_Error;

readonly class Client implements ClientInterface
{
	public function __construct(
		protected RequestFactoryInterface $requestFactory,
		protected ResponseFactoryInterface $responseFactory,
	) {
	}

	public function get(string $url, ?array $headers = null, array|string|null $body = null): ResponseInterface
	{
		return $this->request(
			$this->requestFactory->create('GET', $url, $headers, $body),
		);
	}

	public function head(string $url, ?array $headers = null, array|string|null $body = null): ResponseInterface
	{
		return $this->request(
			$this->requestFactory->create('HEAD', $url, $headers, $body),
		);
	}

	public function post(string $url, ?array $headers = null, array|string|null $body = null): ResponseInterface
	{
		return $this->request(
			$this->requestFactory->create('POST', $url, $headers, $body),
		);
	}

	public function put(string $url, ?array $headers = null, array|string|null $body = null): ResponseInterface
	{
		return $this->request(
			$this->requestFactory->create('PUT', $url, $headers, $body),
		);
	}

	public function patch(string $url, ?array $headers = null, array|string|null $body = null): ResponseInterface
	{
		return $this->request(
			$this->requestFactory->create('PATCH', $url, $headers, $body),
		);
	}

	public function delete(string $url, ?array $headers = null, array|string|null $body = null): ResponseInterface
	{
		return $this->request(
			$this->requestFactory->create('DELETE', $url, $headers, $body),
		);
	}

	public function options(string $url, ?array $headers = null, array|string|null $body = null): ResponseInterface
	{
		return $this->request(
			$this->requestFactory->create('OPTIONS', $url, $headers, $body),
		);
	}

	public function request(RequestInterface $request): ResponseInterface
	{
		$response = wp_safe_remote_request(
			$request->url(),
			[
				'method' => $request->method()->value,
				'headers' => $request->headers(),
				'body' => $request->body(),
			]
		);

		if ($response instanceof WP_Error) {
			throw new ClientException($response->get_error_message());
		}

		return $this->responseFactory->create(
			wp_remote_retrieve_response_code($response),
			wp_remote_retrieve_headers($response)->getAll(),
			wp_remote_retrieve_body($response)
		);
	}
}
