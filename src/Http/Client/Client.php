<?php

namespace Hoo\WordPressPluginFramework\Http\Client;

use Hoo\WordPressPluginFramework\Http\{
	Request\RequestInterface,
	Request\RequestFactoryInterface,
	Response\ResponseInterface,
	Response\ResponseFactoryInterface
};
use WP_Error;

readonly class Client implements ClientInterface
{
	public function __construct(
		protected ResponseFactoryInterface $responseFactory,
	) {
	}

	public function send(RequestInterface $request): ResponseInterface
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

