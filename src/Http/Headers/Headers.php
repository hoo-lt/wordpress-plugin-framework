<?php

namespace Hoo\WordPressPluginFramework\Http\Headers;

use ArrayIterator;
use Traversable;

readonly class Headers implements HeadersInterface
{
	protected array $headers;

	public function __construct(
		array $headers,
	) {
		$this->headers = $this->normalizeHeaders($headers);
	}

	public function with(array $headers): static
	{
		return new static($headers);
	}

	public function without(): static
	{
		return new static([]);
	}

	public function header(string $key): mixed
	{
		return $this->headers[strtolower($key)] ?? null;
	}

	public function withHeader(string $key, mixed $header): static
	{
		$headers = [
			...$this->headers,
			strtolower($key) => $header
		];

		return new static($headers);
	}

	public function withoutHeader(string $key): static
	{
		$headers = $this->headers;
		unset($headers[strtolower($key)]);

		return new static($headers);
	}

	public function toArray(): array
	{
		return $this->headers;
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator($this->headers);
	}

	public function count(): int
	{
		return count($this->headers);
	}

	public function accept(): ?string
	{
		return $this->headers['accept'] ?? null;
	}

	public function contentLength(): ?int
	{
		return $this->headers['content-length'] ?? null;
	}

	public function contentType(): ?string
	{
		return $this->headers['content-type'] ?? null;
	}

	protected function normalizeHeaders(array $headers): array
	{
		$normalizedHeaders = [];

		foreach ($headers as $key => $header) {
			$normalizedHeaders[strtolower($key)] = $header;
		}

		return $normalizedHeaders;
	}
}
