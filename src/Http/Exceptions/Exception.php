<?php

namespace Hoo\WordPressPluginFramework\Http\Exceptions;

class Exception extends \Exception
{
	public function __construct(
		string $message,
		string $code,
		protected int $statusCode,
	) {
		$this->message = $message;
		$this->code = $code;
	}

	public function getStatusCode(): int
	{
		return $this->statusCode;
	}
}