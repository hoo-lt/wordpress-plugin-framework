<?php

namespace Hoo\WordPressPluginFramework\Http\Server\Response;

use Hoo\WordPressPluginFramework\{
	Http\Client,
	Http\Message\Body\BodyInterface,
	Http\Message\Headers\HeadersInterface,
};

readonly class Response extends Client\Response\Response implements ResponseInterface
{
	public function __construct(
		protected int $statusCode,
		protected ?HeadersInterface $headers,
		protected ?BodyInterface $body,
	) {
		parent::__construct($statusCode, $headers, $body);
	}
}
