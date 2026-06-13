<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\KeyValues;

use Hoo\WordPressPluginFramework\Http\Server\Request\RequestInterface;

interface KeyValuesInterface
{
	public function key(): string;
	public function values(RequestInterface $request): ?array;
}
