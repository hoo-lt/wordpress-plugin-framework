<?php

namespace Hoo\WordPressPluginFramework\Http;

interface RequestInterface
{
	public function get(string $key): ?string;
	public function post(string $key): ?string;
}