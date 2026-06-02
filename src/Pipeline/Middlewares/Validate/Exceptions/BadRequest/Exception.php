<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Exceptions\BadRequest;

use Hoo\WordPressPluginFramework\Http\Exceptions\BadRequest\Exception as BadRequestException;

class Exception extends BadRequestException
{
	public function __construct(
		string $message,
		string $code,
	) {
		parent::__construct(
			$message,
			$code,
		);
	}
}