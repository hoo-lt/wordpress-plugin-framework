<?php

namespace Hoo\WordPressPluginFramework\Database\Migration;

use Exception;

class MigrationException extends Exception
{
	public function __construct(string $message)
	{
		parent::__construct($message);
	}
}
