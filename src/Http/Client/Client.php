<?php

namespace Hoo\WordPressPluginFramework\Http\Client;

use Hoo\WordPressPluginFramework\{
	Http\Client\Request\RequestInterface,
	Http\Client\Response\ResponseInterface,
	Http\Client\Response\ResponseFactoryInterface,
};
use WP_Error;

readonly class Client implements ClientInterface
{
	public function __construct(
		protected ResponseFactoryInterface $responseFactory,
	) {
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

		return $this->responseFactory->from(
			$response['response']['code'],
			$response['headers']->getAll(),
			$response['body'],
		);
	}
}
