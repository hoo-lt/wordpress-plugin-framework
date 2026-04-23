<?php

namespace Hoo\WordPressPluginFramework\Http\Url\Query;

use Hoo\WordPressPluginFramework\Helpers;

readonly class Query implements QueryInterface
{
	public function __construct(
		protected Helpers\Array\HelperInterface $arrayHelper,
		protected array $query,
	) {
	}

	public function with(array $query): static
	{
		return new static(
			$this->arrayHelper,
			$query
		);
	}

	public function without(): static
	{
		return new static(
			$this->arrayHelper,
			[]
		);
	}

	public function values(): array
	{
		return $this->query;
	}

	public function value(string $key): mixed
	{
		return $this->arrayHelper->value($this->query, $key);
	}

	public function withValue(string $key, mixed $value): static
	{
		return new static(
			$this->arrayHelper,
			$this->arrayHelper->withValue($this->query, $key, $value)
		);
	}

	public function withoutValue(string $key): static
	{
		return new static(
			$this->arrayHelper,
			$this->arrayHelper->withoutValue($this->query, $key)
		);
	}

	public function __toString(): string
	{
		return http_build_query($this->query, '', '&', PHP_QUERY_RFC3986);
	}
}