<?php

namespace Hoo\WordPressPluginFramework\Http\Exceptions\UnprocessableContent;

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
			422,
			$headers,
			$body,
		);
	}
}