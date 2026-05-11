<?php

namespace Hoo\WordPressPluginFramework\Http\Headers;

use Hoo\WordPressPluginFramework\Http;

readonly class HeadersFactory implements HeadersFactoryInterface
{
	public function __construct(
		protected Http\Server\ServerInterface $server,
	) {
	}

	public function from(array $headers): HeadersInterface
	{
		return new Headers($headers);
	}

	public function fromServer(): ?HeadersInterface
	{
		$headers = $this->server->headers();
		if ($headers === null) {
			return null;
		}

		return $this->from($headers);
	}
}
