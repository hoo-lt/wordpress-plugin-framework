<?php

namespace Hoo\WordPressPluginFramework\Http\Url;

use Hoo\WordPressPluginFramework\Http;

readonly class Url implements UrlInterface
{
	protected string $host;
	protected string $path;
	protected ?int $port;

	public function __construct(
		protected Http\Url\Scheme\Scheme $scheme,
		string $host,
		?int $port,
		string $path,
		protected ?Http\Url\Query\QueryInterface $query,
	) {
		$this->validateHost($host);
		$this->host = $this->normalizeHost($host);

		$this->validatePort($port);
		$this->port = $this->normalizePort($port);

		$this->validatePath($path);
		$this->path = $this->normalizePath($path);
	}

	public function scheme(): Http\Url\Scheme\Scheme
	{
		return $this->scheme;
	}

	public function withScheme(Http\Url\Scheme\Scheme $scheme): static
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

	public function query(): ?Http\Url\Query\QueryInterface
	{
		return $this->query;
	}

	public function withQuery(Http\Url\Query\QueryInterface $query): static
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
			null,
		);
	}

	public function __toString(): string
	{
		$url = "{$this->scheme->value}://{$this->host}";

		if ($this->port !== null) {
			$url .= ":{$this->port}";
		}

		$url .= $this->path;

		if ($this->query !== null) {
			$url .= "?{$this->query}";
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
		// RFC 3986 §3.3: with authority, path must be empty or begin with "/" (path-abempty).
		if (
			$path !== '' &&
			$path[0] !== '/'
		) {
			throw new UrlException('path must be empty or begin with "/" when authority is present');
		}
	}

	protected function normalizePath(string $path): string
	{
		// RFC 3986 §5.2.4: remove_dot_segments. Validation guarantees path is '' or starts with '/'.
		if ($path === '') {
			return '';
		}

		$resolved = [];
		$segments = explode('/', $path);

		foreach ($segments as $segment) {
			if ($segment === '.') {
				continue;
			}

			if ($segment === '..') {
				// Never pop the leading '' that represents the root '/'.
				if (count($resolved) > 1) {
					array_pop($resolved);
				}
				continue;
			}

			$resolved[] = $segment;
		}

		// Preserve trailing slash when the last segment was '.' or '..'.
		$last = end($segments);
		if (
			$last === '.' ||
			$last === '..'
		) {
			$resolved[] = '';
		}

		return implode('/', $resolved);
	}
}