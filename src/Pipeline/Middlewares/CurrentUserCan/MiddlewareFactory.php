<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\CurrentUserCan;

readonly class MiddlewareFactory implements MiddlewareFactoryInterface
{
	public function create(): MiddlewareInterface
	{
		return new Middleware();
	}
}