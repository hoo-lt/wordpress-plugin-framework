<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers;

interface HeadersFactoryInterface
{
	public function create(array $headers): HeadersInterface;
}
