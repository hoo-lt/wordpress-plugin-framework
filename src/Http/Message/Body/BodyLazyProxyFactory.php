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

	public function create(string $contentType, mixed $body): BodyInterface
	{
		return $this->newLazyProxy(fn() => $this->bodyFactory->create($contentType, $body));
	}

	public function createFromEncoded(string $contentType, mixed $body): BodyInterface
	{
		return $this->newLazyProxy(fn() => $this->bodyFactory->createFromEncoded($contentType, $body));
	}

	public function createFromUnnormalized(string $contentType, mixed $body): BodyInterface
	{
		return $this->newLazyProxy(fn() => $this->bodyFactory->createFromUnnormalized($contentType, $body));
	}

	protected function newLazyProxy(Closure $closure): BodyInterface
	{
		return new ReflectionClass(Body::class)->newLazyProxy($closure);
	}
}
