<?php

namespace Hoo\WordPressPluginFramework\Http\Body\Stream;

use Hoo\WordPressPluginFramework\Http;

readonly class Body implements Http\Body\BodyInterface
{
	public function __construct(
		protected mixed $stream,
	) {
		if (
			!is_resource($stream) ||
			get_resource_type($stream) !== 'stream'
		) {
			throw new \InvalidArgumentException();
		}
	}

	public function values(string $key = ''): array
	{
		if ($key !== '') {
			throw new Http\Body\BodyException('cant perform on scalar');
		}

		return [
			'' => (string) $this,
		];
	}

	public function value(string $key = ''): mixed
	{
		if ($key !== '') {
			throw new Http\Body\BodyException('cant perform on scalar');
		}

		return (string) $this;
	}

	public function withValue(string $key, mixed $value): static
	{
		throw new Http\Body\BodyException('cant perform on scalar');
	}

	public function withoutValue(string $key): static
	{
		throw new Http\Body\BodyException('cant perform on scalar');
	}

	public function __toString(): string
	{
		return $this->stream;
	}
}