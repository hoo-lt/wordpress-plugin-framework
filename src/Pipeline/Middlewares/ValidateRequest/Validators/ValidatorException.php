<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Validators;

use Exception;

class ValidatorException extends Exception
{
	public function __construct(
		protected array $errors,
	) {
		parent::__construct('Validation failed', 'validate_request_error');
	}
}
