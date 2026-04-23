<?php

namespace Hoo\WordPressPluginFramework\Http\Headers;

use Hoo\WordPressPluginFramework\Helpers;

readonly class Headers implements HeadersInterface
{
	protected array $headers;

	public function __construct(
		protected Helpers\Array\HelperInterface $arrayHelper,
		array $headers
	) {
		$this->headers = $this->normalizeHeaders($headers);
	}

	public function with(array $headers): static
	{
		return new static(
			$this->arrayHelper,
			$headers
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
		return $this->headers;
	}

	public function value(string $key): mixed
	{
		return $this->arrayHelper->value($this->headers, strtolower($key));
	}

	public function withValue(string $key, mixed $value): static
	{
		return new static(
			$this->arrayHelper,
			$this->arrayHelper->withValue($this->headers, strtolower($key), $value)
		);
	}

	public function withoutValue(string $key): static
	{
		return new static(
			$this->arrayHelper,
			$this->arrayHelper->withoutValue($this->headers, strtolower($key)),
		);
	}

	protected function normalizeHeaders(array $headers): array
	{
		$normalizedHeaders = [];

		foreach ($headers as $key => $value) {
			$normalizedHeaders[strtolower($key)] = $value;
		}

		return $normalizedHeaders;
	}
}
