<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Parameters;

use ArrayIterator;
use Hoo\WordPressPluginFramework\Http\Semantics\Semantics;
use Traversable;

readonly class Parameters implements ParametersInterface
{
	protected array $parameters;

	public function __construct(
		array $parameters,
	) {
		$this->parameters = $this->normalize($parameters);
	}

	public function parameter(string $name): ?string
	{
		return $this->parameters[strtolower($name)] ?? null;
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator($this->parameters);
	}

	public function count(): int
	{
		return count($this->parameters);
	}

	public function __toString(): string
	{
		$parameters = '';

		foreach ($this->parameters as $name => $value) {
			$parameters .= ";{$name}={$this->quote($value)}";
		}

		return $parameters;
	}

	protected function normalize(array $parameters): array
	{
		$normalized = [];

		foreach ($parameters as $name => $value) {
			$normalized[strtolower($name)] = $value;
		}

		return $normalized;
	}

	protected function quote(string $value): string
	{
		if (preg_match('/\A' . Semantics::TOKEN . '\z/', $value)) {
			return $value;
		}

		return '"' . preg_replace('/(?!' . Semantics::QDTEXT . ')./s', "\x5C\x5C\$0", $value) . '"';
	}
}
