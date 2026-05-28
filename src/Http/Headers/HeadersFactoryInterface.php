<?php

namespace Hoo\WordPressPluginFramework\Http\Headers;

use Hoo\WordPressPluginFramework\Http\Request\RequestInterface;
use Throwable;

interface HeadersFactoryInterface
{
	public function from(array $headers): HeadersInterface;
	public function tryFrom(?array $headers): ?HeadersInterface;
}
