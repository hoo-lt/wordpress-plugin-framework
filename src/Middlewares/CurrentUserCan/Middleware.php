<?php

namespace Hoo\WordPressPluginFramework\Middlewares\CurrentUserCan;

use Hoo\WordPressPluginFramework\Middlewares\MiddlewareException;
use Hoo\WordPressPluginFramework\Middlewares\MiddlewareInterface;

readonly class Middleware implements MiddlewareInterface
{
	public function __construct(
		protected Capability\CapabilityInterface $capability,
	) {
	}

	public function __invoke(callable $callable): mixed
	{
		if (!current_user_can($this->capability->value)) {
			throw new MiddlewareException('can not');
		}

		return $callable();
	}
}