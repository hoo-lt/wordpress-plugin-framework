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

	public function header(string $key): mixed
	{
		return $this->headers[strtolower($key)] ?? null;
	}

	public function withHeader(string $key, mixed $header): static
	{
		$headers = $this->headers;
		$headers[strtolower($key)] = $header;

		return new static($headers);
	}

	public function withoutHeader(string $key): static
	{
		$headers = $this->headers;
		unset($headers[strtolower($key)]);

		return new static($headers);
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
		$accept = $this->headers['accept'] ?? null;
		return $accept;
	}

	public function contentLength(): ?int
	{
		$contentLength = $this->headers['content-length'] ?? null;
		return $contentLength;
	}

	public function contentType(): ?string
	{
		$contentType = $this->headers['content-type'] ?? null;
		return $contentType;
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
