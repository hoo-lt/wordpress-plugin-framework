<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\VerifyNonce;

use Hoo\WordPressPluginFramework\Pipeline\Middlewares;

interface MiddlewareInterface extends Middlewares\MiddlewareInterface
{
	public function withName(string $name): static;
	public function withAction(string|int $action): static;
}