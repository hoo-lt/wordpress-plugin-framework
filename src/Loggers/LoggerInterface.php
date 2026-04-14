<?php

namespace Hoo\WordPressPluginFramework\Loggers;

interface LoggerInterface
{
	public function info(string $message): void;
}