<?php

namespace Hoo\WordPressPluginFramework\Http\Exceptions\Forbidden;

use Hoo\WordPressPluginFramework\Http;

class Exception extends Http\Exceptions\Exception
{
	public function __construct(
		string $message,
		string $code,
		?array $headers = null,
		?array $body = null,
	) {
		parent::__construct(
			$message,
			$code,
			403,
			$headers,
			$body,
		);
	}
}