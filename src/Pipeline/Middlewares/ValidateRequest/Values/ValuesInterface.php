<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Values;

use Hoo\WordPressPluginFramework\Http\Request\RequestInterface;

interface ValuesInterface
{
	public function __invoke(RequestInterface $request): array;
}
