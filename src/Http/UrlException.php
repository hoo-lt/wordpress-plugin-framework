<?php

namespace Hoo\WordPressPluginFramework\Http;

use Exception;

class UrlException extends Exception
{
	public function __construct(string $message)
	{
		parent::__construct($message);
	}
}