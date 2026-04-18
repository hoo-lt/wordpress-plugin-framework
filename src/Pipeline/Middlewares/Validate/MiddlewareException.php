<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate;

use Hoo\WordPressPluginFramework\Pipeline\Middlewares;

class MiddlewareException extends Middlewares\MiddlewareException
{
	public function __construct(
		protected array $errors,
	) {
		parent::__construct('Validation failed');
	}

	public function errors(): array
	{
		return $this->errors;
	}
}
