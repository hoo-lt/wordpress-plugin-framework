<?php

namespace Hoo\WordPressPluginFramework\Http\Headers;

readonly class Headers implements HeadersInterface
{
	protected array $headers;

	public function __construct(array $headers)
	{
		$normalized = [];

		foreach ($headers as $name => $value) {
			$normalized[strtolower($name)] = $value;
		}

		$this->headers = $normalized;
	}

	public function values(): array
	{
		return $this->headers;
	}

	public function value(string $name): ?string
	{
		return $this->headers[strtolower($name)] ?? null;
	}

	public function with(array $headers): static
	{
		return new static($headers);
	}

	public function without(): static
	{
		return new static([]);
	}

	public function withValue(string $name, string $value): static
	{
		return new static([
			...$this->headers,
			strtolower($name) => $value,
		]);
	}

	public function withoutValue(string $name): static
	{
		$headers = $this->headers;
		unset($headers[strtolower($name)]);

		return new static($headers);
	}
}
