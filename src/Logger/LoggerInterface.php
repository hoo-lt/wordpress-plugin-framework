<?php

namespace Hoo\WordpressPluginFramework\Logger;

interface LoggerInterface
{
	public function info(string $message): void;
}