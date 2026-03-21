<?php

namespace Hoo\WordPressPluginFramework\Database\Query;

use Exception;

class QueryException extends Exception
{
	public function __construct(string $message)
	{
		parent::__construct($message);
	}
}