<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers;

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

	public function header(string $name): ?string
	{
		return $this->headers[strtolower($name)] ?? null;
	}

	public function withHeader(string $name, string $header): static
	{
		$headers = $this->headers;
		$headers[strtolower($name)] = $header;

		return new static($headers);
	}

	public function withoutHeader(string $name): static
	{
		$headers = $this->headers;
		unset($headers[strtolower($name)]);

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

		foreach ($headers as $name => $header) {
			$normalizedHeaders[strtolower($name)] = trim($header, " \t");
		}

		return $normalizedHeaders;
	}
}
