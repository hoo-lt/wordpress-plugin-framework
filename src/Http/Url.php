<?php

namespace Hoo\WordPressPluginFramework\Http;

readonly class Url implements UrlInterface
{
	protected function __construct(
		protected Url\Scheme $scheme,
		protected string $host,
		protected ?int $port,
		protected string $path,
		protected ?array $query,
	) {
	}

	public static function from(string $url): UrlInterface
	{
		return new self(
			...self::parse($url)
		);
	}

	public function scheme(): string
	{
		return $this->scheme->value;
	}

	public function withScheme(string $scheme): UrlInterface
	{
		$scheme = self::parseScheme($scheme);

		return new self(
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

	public function withHost(string $host): UrlInterface
	{
		$host = self::parseHost($host);

		return new self(
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

	public function withPort(?int $port): UrlInterface
	{
		$port = self::parsePort($port);

		return new self(
			$this->scheme,
			$this->host,
			$port,
			$this->path,
			$this->query,
		);
	}

	public function path(): string
	{
		return $this->path;
	}

	public function withPath(string $path): UrlInterface
	{
		$path = self::parsePath($path);

		return new self(
			$this->scheme,
			$this->host,
			$this->port,
			$path,
			$this->query,
		);
	}

	public function query(): ?string
	{
		return $this->query ? http_build_query($this->query, PHP_QUERY_RFC3986) : null;
	}

	public function withQuery(?string $query): UrlInterface
	{
		$query = self::parseQuery($query);

		return new self(
			$this->scheme,
			$this->host,
			$this->port,
			$this->path,
			$query,
		);
	}

	public function queryValue(string $key): ?string
	{
		return $this->query[$key] ?? null;
	}

	public function withQueryValue(string $key, ?string $value): UrlInterface
	{
		$query = $this->query;

		if ($value !== null) {
			$query ??= [];
			$query[$key] = $value;
		} else {
			unset($query[$key]);
		}

		return new self(
			$this->scheme,
			$this->host,
			$this->port,
			$this->path,
			$query,
		);
	}

	public function __toString(): string
	{
		$scheme = $this->scheme();
		$host = $this->host();
		$port = $this->port();
		$path = $this->path();
		$query = $this->query();

		$url = "{$scheme}://{$host}";

		if ($port !== null) {
			$url .= ":{$port}";
		}

		$url .= $path;

		if ($query !== null) {
			$url .= "?{$query}";
		}

		return $url;
	}

	protected static function parse(string $url): array
	{
		$url = parse_url($url);
		if ($url) {
			return [
				'scheme' => self::parseScheme($url['scheme'] ?? ''),
				'host' => self::parseHost($url['host'] ?? ''),
				'port' => self::parsePort($url['port'] ?? null),
				'path' => self::parsePath($url['path'] ?? ''),
				'query' => self::parseQuery($url['query'] ?? null),
			];
		}

		throw new UrlException('url cannot be empty string');
	}

	protected static function parseScheme(string $scheme): Url\Scheme
	{
		if ($scheme !== '') {
			return Url\Scheme::from($scheme);
		}

		throw new UrlException('scheme is mandatory');
	}

	protected static function parseHost(string $host): string
	{
		if ($host !== '') {
			return $host;
		}

		throw new UrlException('host is mandatory');
	}

	protected static function parsePort(?int $port): ?int
	{

		if ($port === null) {
			return $port;
		}

		if (
			$port >= 1 &&
			$port <= 65535
		) {
			return $port;
		}

		throw new UrlException('invalid port');
	}

	protected static function parsePath(string $path): string
	{
		if ($path === '') {
			return $path;
		}

		return '/' . ltrim($path, '/');
	}

	protected static function parseQuery(?string $query): ?array
	{
		if ($query === null) {
			return $query;
		}

		parse_str($query, $query);
		return $query;
	}
}