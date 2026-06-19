<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators;

use Closure;
use Hoo\WordPressPluginFramework\Http\Server\Request\RequestInterface;

interface ValidatorInterface
{
	public function validate(RequestInterface $request, Closure $closure): void;
}
