<?php

namespace Hoo\WordPressPluginFramework\Json;

use Exception;

class JsonException extends Exception
{
	public function __construct(string $message)
	{
		parent::__construct($message);
	}
}