<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\VerifyNonce;

interface MiddlewareFactoryInterface
{
	public function create(): MiddlewareInterface;
}