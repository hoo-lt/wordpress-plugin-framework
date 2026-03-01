<?php

namespace Hoo\WordPressPluginFramework\Http;

interface RequestInterface
{
	public function get(string $name): ?string;
	public function post(string $name): ?string;
}