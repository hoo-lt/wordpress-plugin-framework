<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\VerifyNonce;

readonly class MiddlewareFactory implements MiddlewareFactoryInterface
{
	public function create(): MiddlewareInterface
	{
		return new Middleware();
	}
}