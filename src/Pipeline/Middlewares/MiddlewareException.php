<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares;

use Exception;

class MiddlewareException extends Exception
{
	public function __construct(
		string $message,
		string $code,
	) {
		$this->message = $message;
		$this->code = $code;
	}
}