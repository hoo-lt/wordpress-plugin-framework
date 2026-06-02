<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\CurrentUserCan;

interface MiddlewareFactoryInterface
{
	public function create(): MiddlewareInterface;
}