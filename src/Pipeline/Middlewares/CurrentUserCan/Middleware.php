<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\CurrentUserCan;

use Closure;
use Hoo\WordPressPluginFramework\{
	Collections\Message\Collection as MessageCollection,
	Http\Server\Request\RequestInterface,
	Pipeline\Middlewares\CurrentUserCan\Capability\Capability,
	Pipeline\Middlewares\MiddlewareException,
};

readonly class Middleware implements MiddlewareInterface
{
	public function __construct(
		protected array $capabilities = [],
	) {
	}

	public function withCapabilities(Capability ...$capabilities): static
	{
		return new static($capabilities);
	}

	public function withoutCapabilities(): static
	{
		return new static([]);
	}

	public function withCapability(Capability $capability): static
	{
		return $this->withCapabilities(...$this->capabilities, $capability);
	}

	public function __invoke(RequestInterface $request, Closure $closure): mixed
	{
		if ($this->capabilities === []) {
			throw new MiddlewareException('middleware misconfigured');
		}

		$messages = new MessageCollection();

		foreach ($this->capabilities as $capability) {
			if (!current_user_can($capability->value)) {
				$messages->add($capability->value, 'not satisfied');
			}
		}

		if ($messages->isNotEmpty()) {
			throw new Exceptions\Forbidden\Exception('can not', 'current_user_can_error', $messages);
		}

		return $closure($request);
	}
}