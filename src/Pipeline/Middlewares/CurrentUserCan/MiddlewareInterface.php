<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\CurrentUserCan;

use Hoo\WordPressPluginFramework\{
	Pipeline\Middlewares,
	Pipeline\Middlewares\CurrentUserCan\Capability\Capability,
};

interface MiddlewareInterface extends Middlewares\MiddlewareInterface
{
	public function withCapabilities(Capability ...$capabilities): static;
	public function withCapability(Capability $capability): static;
}