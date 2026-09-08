<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body;

use Closure;
use ReflectionClass;

readonly class BodyLazyProxyFactory implements BodyFactoryInterface
{
	public function __construct(
		protected BodyFactoryInterface $bodyFactory,
	) {
	}

	public function createBody(string $contentType, mixed $body): BodyInterface
	{
		return $this->newLazyProxy(fn() => $this->bodyFactory->createBody($contentType, $body));
	}

	public function createBodyFromEncoded(string $contentType, mixed $body): BodyInterface
	{
		return $this->newLazyProxy(fn() => $this->bodyFactory->createBodyFromEncoded($contentType, $body));
	}

	public function createBodyFromUnnormalized(string $contentType, mixed $body): BodyInterface
	{
		return $this->newLazyProxy(fn() => $this->bodyFactory->createBodyFromUnnormalized($contentType, $body));
	}

	public function createBodiesFromUnnormalized(mixed $body): array
	{
		return $this->bodyFactory->createBodiesFromUnnormalized($body);
	}

	protected function newLazyProxy(Closure $closure): BodyInterface
	{
		return new ReflectionClass(Body::class)->newLazyProxy($closure);
	}
}
