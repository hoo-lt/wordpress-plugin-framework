<?php

namespace Hoo\WordPressPluginFramework\Http\Server\Request;

use Hoo\WordPressPluginFramework\{
	Http\Client,
	Http\Server\Request\Routes\RoutesInterface,
};

interface RequestInterface extends Client\Request\RequestInterface
{
	public function routes(): ?RoutesInterface;
	public function withRoutes(RoutesInterface $routes): static;
	public function withoutRoutes(): static;

	public function route(string $key): mixed;
}
