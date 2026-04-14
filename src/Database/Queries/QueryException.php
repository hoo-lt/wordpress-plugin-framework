<?php

namespace Hoo\WordPressPluginFramework\Database\Queries;

use Exception;

class QueryException extends Exception
{
	public function __construct(string $message)
	{
		parent::__construct($message);
	}
}