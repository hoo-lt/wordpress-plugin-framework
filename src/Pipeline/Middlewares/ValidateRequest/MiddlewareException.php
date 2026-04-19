<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest;

use Hoo\WordPressPluginFramework\Pipeline\Middlewares;

class MiddlewareException extends Middlewares\MiddlewareException
{
	public function __construct(
		protected array $errors,
	) {
		parent::__construct('Validation failed', 'validate_request_error');
	}

	public function errors(): array
	{
		return $this->errors;
	}
}
