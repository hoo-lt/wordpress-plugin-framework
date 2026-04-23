<?php

namespace Hoo\WordPressPluginFramework\Http\Client;

use WP_Error;

readonly class Client
{
	public function __construct(
		protected Response\ResponseFactoryInterface $responseFactory,
	) {
	}

	public function send(Request\RequestInterface $request): Response\ResponseInterface
	{
		$response = wp_safe_remote_request(
			(string) $request->url(),
			[
				'method' => $request->method()->value,
				'headers' => $request->headers(),
				'body' => $request->body(),
			]
		);

		if ($response instanceof WP_Error) {
			throw new ClientException($response->get_error_message());
		}

		return $this->responseFactory->fromArray(
			$response['headers']->getAll(),
			$response['body'],
			$response['response']['code'],
		);
	}
}

