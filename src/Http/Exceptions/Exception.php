<?php

namespace Hoo\WordPressPluginFramework\Http\Exceptions;

class Exception extends \Exception
{
	public function __construct(
		string $message,
		string $code,
		protected int $statusCode,
		protected ?array $headers = null,
	) {
		$this->message = $message;
		$this->code = $code;
	}

	public function getStatusCode(): int
	{
		return $this->statusCode;
	}

	public function getHeaders(): ?array
	{
		return $this->headers;
	}
}