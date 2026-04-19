<?php

namespace Hoo\WordPressPluginFramework\Http\Request;

use Hoo\WordPressPluginFramework\Http\Method\Method;

interface RequestInterface
{
	public function method(): Method;
	public function contentType(): ?string;
	public function query(?string $key = null): mixed;
	public function body(?string $key = null): mixed;
	public function bodyValues(string $key): array;
	public function queryValues(string $key): array;
}