<?php

namespace Hoo\WordPressPluginFramework\Http\Client;

use Hoo\WordPressPluginFramework\Http;
use WP_Error;

readonly class Client implements ClientInterface
{
	public function __construct(
		protected Http\Response\ResponseFactoryInterface $responseFactory,
	) {
	}

	public function send(Http\Request\RequestInterface $request): Http\Response\ResponseInterface
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

		return $this->responseFactory->from(
			$response['response']['code'],
			$response['headers']->getAll(),
			$response['body'],
		);
	}
}
