<?php

namespace Hoo\WordPressPluginFramework\Http\Headers;

use Hoo\WordPressPluginFramework\Http;

readonly class HeadersFactory implements HeadersFactoryInterface
{
	public function from(array $headers): HeadersInterface
	{
		return new Headers($headers);
	}

	public function tryFrom(?array $headers): ?HeadersInterface
	{
		if ($headers === null) {
			return null;
		}

		return $this->from($headers);
	}
}
