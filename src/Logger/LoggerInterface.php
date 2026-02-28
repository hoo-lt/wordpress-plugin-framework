<?php

namespace Hoo\WordPressPluginFramework\Logger;

interface LoggerInterface
{
	public function info(string $message): void;
}