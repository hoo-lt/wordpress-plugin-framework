<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\KeyValue;

use Hoo\WordPressPluginFramework\Http\Server\Request\RequestInterface;

interface KeyValueInterface
{
	public function key(): string;

	public function values(RequestInterface $request): ?array;
	public function value(RequestInterface $request): mixed;
}
