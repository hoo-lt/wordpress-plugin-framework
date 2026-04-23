<?php

namespace Hoo\WordPressPluginFramework\Http\Url;

use Hoo\WordPressPluginFramework\Http\Url\Scheme\Scheme;

readonly class Url implements UrlInterface
{
	protected string $host;
	protected string $path;
	protected ?int $port;

	public function __construct(
		protected Scheme $scheme,
		string $host,
		?int $port,
		string $path,
		protected array $query,
	) {
		$this->validateHost($host);
		$this->host = $this->normalizeHost($host);

		$this->validatePort($port);
		$this->port = $this->normalizePort($port);

		$this->validatePath($path);
		$this->path = $this->normalizePath($path);
	}

	public function scheme(): Scheme
	{
		return $this->scheme;
	}

	public function withScheme(Scheme $scheme): static
	{
		return new static(
			$scheme,
			$this->host,
			$this->port,
			$this->path,
			$this->query,
		);
	}

	public function host(): string
	{
		return $this->host;
	}

	public function withHost(string $host): static
	{
		return new static(
			$this->scheme,
			$host,
			$this->port,
			$this->path,
			$this->query,
		);
	}

	public function port(): ?int
	{
		return $this->port;
	}

	public function withPort(int $port): static
	{
		return new static(
			$this->scheme,
			$this->host,
			$port,
			$this->path,
			$this->query,
		);
	}

	public function withoutPort(): static
	{
		return new static(
			$this->scheme,
			$this->host,
			null,
			$this->path,
			$this->query,
		);
	}

	public function path(): string
	{
		return $this->path;
	}

	public function withPath(string $path): static
	{
		return new static(
			$this->scheme,
			$this->host,
			$this->port,
			$path,
			$this->query,
		);
	}

	public function query(): array
	{
		return $this->query;
	}

	public function withQuery(array $query): static
	{
		return new static(
			$this->scheme,
			$this->host,
			$this->port,
			$this->path,
			$query,
		);
	}

	public function withoutQuery(): static
	{
		return new static(
			$this->scheme,
			$this->host,
			$this->port,
			$this->path,
			[],
		);
	}

	public function withQueryValue(string $key, string $value): static
	{
		$query = $this->query;
		$query = [
			...$query,
			$key => $value,
		];

		return new static(
			$this->scheme,
			$this->host,
			$this->port,
			$this->path,
			$query,
		);
	}

	public function withoutQueryValue(string $key): static
	{
		$query = $this->query;
		unset($query[$key]);

		return new static(
			$this->scheme,
			$this->host,
			$this->port,
			$this->path,
			$query,
		);
	}

	public function __toString(): string
	{
		$url = "{$this->scheme->value}://{$this->host}";

		if ($this->port !== null) {
			$url .= ":{$this->port}";
		}

		$url .= $this->path;

		if ($this->query !== []) {
			$url .= '?' . http_build_query($this->query, '', '&', PHP_QUERY_RFC3986);
		}

		return $url;
	}

	protected function validateHost(string $host): void
	{
		if ($host === '') {
			throw new UrlException('host is mandatory');
		}
	}

	protected function normalizeHost(string $host): string
	{
		return strtolower($host);
	}

	protected function validatePort(?int $port): void
	{
		if (
			$port !== null && (
				$port < 1 ||
				$port > 65535
			)
		) {
			throw new UrlException('port must be within range');
		}
	}

	protected function normalizePort(?int $port): ?int
	{
		return $port === $this->scheme->port() ? null : $port;
	}

	protected function validatePath(string $path): void
	{
		if (
			$path !== '' &&
			$path[0] !== '/'
		) {
			throw new UrlException('Path must be empty or start with "/" when host is present');
		}
	}

	protected function normalizePath(string $path): string
	{
		$resolved = [];

		$segments = explode('/', $path);
		foreach ($segments as $segment) {
			if ($segment === '..') {
				array_pop($resolved);
			} elseif ($segment !== '.') {
				$resolved[] = $segment;
			}
		}

		$last = end($segments);
		if (
			$last === '..' ||
			$last === '.'
		) {
			$resolved[] = '';
		}

		return implode('/', $resolved);
	}
}