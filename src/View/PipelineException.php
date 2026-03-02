<?php

namespace Hoo\WordPressPluginFramework\View;

use Exception;

class ViewException extends Exception
{
	public function __construct(string $message)
	{
		parent::__construct($message);
	}
}