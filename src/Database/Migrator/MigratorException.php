<?php

namespace Hoo\WordPressPluginFramework\Database\Migrator;

use Exception;

class MigratorException extends Exception
{
	public function __construct(string $message)
	{
		parent::__construct($message);
	}
}