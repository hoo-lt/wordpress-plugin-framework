<?php

namespace Hoo\WordPressPluginFramework\Middlewares;

use Exception;

class MiddlewareException extends Exception
{
	public function __construct(string $message)
	{
		parent::__construct($message);
	}
}