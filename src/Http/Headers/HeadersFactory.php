<?php

namespace Hoo\WordPressPluginFramework\Http\Headers;

use Hoo\WordPressPluginFramework\Helpers;

readonly class HeadersFactory implements HeadersFactoryInterface
{
	public function __construct(
		protected Helpers\Array\HelperInterface $arrayHelper,
	) {
	}

	public function from(array $headers): HeadersInterface
	{
		return new Headers(
			$this->arrayHelper,
			$headers
		);
	}

	public function fromServer(array $server): HeadersInterface
	{
		$headers = [];

		foreach ($server as $key => $value) {
			if (!str_starts_with($key, 'HTTP_')) {
				continue;
			}

			$key = strtr($key, [
				'HTTP_' => '',
				'_' => '-',
			]);

			$headers[$key] = $value;
		}

		if (isset($server['CONTENT_TYPE'])) {
			$headers['CONTENT-TYPE'] = $server['CONTENT_TYPE'];
		}

		if (isset($server['CONTENT_LENGTH'])) {
			$headers['CONTENT-LENGTH'] = $server['CONTENT_LENGTH'];
		}

		return new Headers(
			$this->arrayHelper,
			$headers
		);
	}
}
