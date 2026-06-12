<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers;

readonly class HeadersFactory implements HeadersFactoryInterface
{
	public function create(array $headers): HeadersInterface
	{
		return new Headers($headers);
	}

	public function tryCreate(?array $headers): ?HeadersInterface
	{
		if ($headers === null) {
			return null;
		}

		return $this->create($headers);
	}
}
