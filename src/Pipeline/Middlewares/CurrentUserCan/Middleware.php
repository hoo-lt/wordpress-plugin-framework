<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\CurrentUserCan;

use Closure;
use Hoo\WordPressPluginFramework\{
	Http,
	Pipeline,
};

readonly class Middleware implements Pipeline\Middlewares\MiddlewareInterface
{
	use Pipeline\Middlewares\MiddlewareTrait;

	public function __construct(
		protected Capability\Capability $capability,
	) {
	}

	public function __invoke(Http\Request\RequestInterface $request, Closure $closure): mixed
	{
		if (!current_user_can($this->capability->value)) {
			throw new Pipeline\Middlewares\MiddlewareException('can not', 'current_user_can_error');
		}

		return $closure($request);
	}
}