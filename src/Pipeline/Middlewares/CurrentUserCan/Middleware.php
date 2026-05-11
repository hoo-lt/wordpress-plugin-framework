<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\CurrentUserCan;

use Closure;
use Hoo\WordPressPluginFramework\Http\Request\RequestInterface;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareException;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareTrait;

readonly class Middleware implements MiddlewareInterface
{
	use MiddlewareTrait;

	public function __construct(
		protected Capability\Capability $capability,
	) {
	}

	public function __invoke(RequestInterface $request, Closure $closure): mixed
	{
		if (!current_user_can($this->capability->value)) {
			throw new MiddlewareException('can not', 'current_user_can_error');
		}

		return $closure($request);
	}
}