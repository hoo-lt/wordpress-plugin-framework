<?php

namespace Hoo\WordPressPluginFramework\Http\Headers;

readonly class HeadersFactory implements HeadersFactoryInterface
{
	public function from(array $headers): HeadersInterface
	{
		return new Headers($headers);
	}

	public function fromServer(array $server): HeadersInterface
	{
		$headers = [];

		foreach ($server as $key => $value) {
			if (str_starts_with($key, 'HTTP_')) {
				$headers[strtolower(str_replace('_', '-', substr($key, 5)))] = $value;
			}
		}

		if (isset($server['CONTENT_TYPE'])) {
			$headers['content-type'] = $server['CONTENT_TYPE'];
		}

		return new Headers($headers);
	}
}
