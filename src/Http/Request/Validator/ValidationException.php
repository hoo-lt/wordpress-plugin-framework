<?php

namespace Hoo\WordPressPluginFramework\Http\Request\Validator;

use Exception;

class ValidationException extends Exception
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
