<?php

namespace Hoo\WordPressPluginFramework\Http\Url\Query;

use Hoo\WordPressPluginFramework\{
	Helpers,
	Http,
};

readonly class Query implements QueryInterface
{
	public function __construct(
		protected Helpers\Array\HelperInterface $arrayHelper,
		protected Http\Coders\Query\Coder $queryCoder,
		protected array $query,
	) {
	}

	public function values(string $key): array
	{
		return $this->arrayHelper->values(
			$this->query,
			$key
		);
	}

	public function value(string $key): mixed
	{
		return $this->arrayHelper->value(
			$this->query,
			$key
		);
	}

	public function withValue(string $key, mixed $value): static
	{
		return new static(
			$this->arrayHelper,
			$this->queryCoder,
			$this->arrayHelper->withValue(
				$this->query,
				$key,
				$value
			)
		);
	}

	public function withoutValue(string $key): static
	{
		return new static(
			$this->arrayHelper,
			$this->queryCoder,
			$this->arrayHelper->withoutValue(
				$this->query,
				$key
			)
		);
	}

	public function __toString(): string
	{
		return $this->queryCoder->encode($this->query);
	}
}