<?php

namespace Hoo\WordPressPluginFramework\Http\Server\Request;

use Hoo\WordPressPluginFramework\{
	Http\Client\Request\RequestInterface as ClientRequestInterface,
	Http\Server\Request\Routes\RoutesInterface,
	Uuid\UuidInterface,
};

interface RequestInterface extends ClientRequestInterface
{
	public function uuid(): UuidInterface;

	public function routes(): ?RoutesInterface;
	public function withRoutes(RoutesInterface $routes): static;
	public function withoutRoutes(): static;

	public function route(string $key): mixed;
}
