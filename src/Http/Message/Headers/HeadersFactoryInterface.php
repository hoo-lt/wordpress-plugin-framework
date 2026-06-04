<?php

namespace Hoo\WordPressPluginFramework\Http\Headers;

interface HeadersFactoryInterface
{
	public function from(array $headers): HeadersInterface;
	public function tryFrom(?array $headers): ?HeadersInterface;
}
