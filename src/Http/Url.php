<?php

namespace Hoo\WordPressPluginFramework\Http;

readonly class Url implements UrlInterface
{
	protected function __construct(
		protected string $scheme,
		protected string $host,
		protected ?int $port,
		protected string $path,
		protected ?string $query,
	) {
		if (
			$scheme !== 'http' &&
			$scheme !== 'https'
		) {
			throw new UrlException('http(s) only supported');
		}

		if ($host === '') {
			throw new UrlException('host is mandatory');
		}

		if (
			$port !== null &&
			(
				$port < 1 ||
				$port > 65535
			)
		) {
			throw new UrlException('port must be within range');
		}

		if (
			$path !== '' &&
			$path[0] !== '/'
		) {
			throw new UrlException('Path must be empty or start with "/" when host is present');
		}
	}

	public static function from(string $url): UrlInterface
	{
		return new self(
			...self::parse($url)
		);
	}

	public function scheme(): string
	{
		return $this->scheme;
	}

	public function withScheme(string $scheme): UrlInterface
	{
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

	public function withPort(int $port): UrlInterface
	{
		return new self(
			$this->scheme,
			$this->host,
			$port,
			$this->path,
			$this->query,
		);
	}

	public function withoutPort(): UrlInterface
	{
		return new self(
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

	public function withPath(string $path): UrlInterface
	{
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
		return $this->query;
	}

	public function withQuery(string $query): UrlInterface
	{
		return new self(
			$this->scheme,
			$this->host,
			$this->port,
			$this->path,
			$query,
		);
	}

	public function withoutQuery(): UrlInterface
	{
		return new self(
			$this->scheme,
			$this->host,
			$this->port,
			$this->path,
			null,
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
		if ($url === '') {
			throw new UrlException('url cannot be empty string');
		}

		$url = parse_url($url);
		if (!$url) {
			throw new UrlException('seriously darmaged url');
		}

		return [
			'scheme' => $url['scheme'] ?? '',
			'host' => $url['host'] ?? '',
			'port' => $url['port'] ?? null,
			'path' => $url['path'] ?? '',
			'query' => $url['query'] ?? null,
		];
	}
}