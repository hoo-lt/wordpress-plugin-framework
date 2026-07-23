<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers;

readonly class HeadersFactory implements HeadersFactoryInterface
{
	public function create(array $headers): HeadersInterface
	{
		return new Headers($headers);
	}
}
