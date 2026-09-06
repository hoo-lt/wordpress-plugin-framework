<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers;

use ArrayIterator;
use Hoo\WordPressPluginFramework\Http\Abnf\Rfc9110;
use Traversable;

readonly class Headers implements HeadersInterface
{
	protected array $headers;

	public function __construct(
		array $headers = [],
	) {
		$this->validateHeaders($headers);
		$this->headers = $this->normalizeHeaders($headers);
	}

	public function has(string $name): bool
	{
		return isset($this->headers[strtolower($name)]);
	}

	public function get(string $name): ?string
	{
		return $this->headers[strtolower($name)] ?? null;
	}

	public function with(string $name, string $value): static
	{
		$headers = $this->headers;
		$headers[strtolower($name)] = $value;

		return new static($headers);
	}

	public function without(string $name): static
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

	protected function normalizeHeaders(array $headers): array
	{
		return array_change_key_case($headers, CASE_LOWER);
	}

	protected function validateHeaders(array $headers): void
	{
		foreach ($headers as $name => $value) {
			if (preg_match('/\A' . Rfc9110::FIELD_NAME . '\z/', $name) !== 1) {
				throw new HeadersException("invalid field name \"{$name}\"");
			}

			if (preg_match('/\A' . Rfc9110::FIELD_VALUE . '\z/', $value) !== 1) {
				throw new HeadersException("invalid field value for \"{$name}\"");
			}
		}
	}
}
