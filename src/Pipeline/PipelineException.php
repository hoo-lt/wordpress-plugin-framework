<?php

namespace Hoo\WordPressPluginFramework\Pipeline;

use Exception;

class PipelineException extends Exception
{
	public function __construct(string $message)
	{
		parent::__construct($message);
	}
}