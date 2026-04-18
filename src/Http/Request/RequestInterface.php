<?php

namespace Hoo\WordPressPluginFramework\Http\Request;

interface RequestInterface
{
	public function get(string $key): ?string;
	public function post(string $key): ?string;
}