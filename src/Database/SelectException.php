<?php

namespace Hoo\WordPressPluginFramework\Database;

use Exception;

class SelectException extends Exception
{
	public function __construct(string $message)
	{
		parent::__construct($message);
	}
}