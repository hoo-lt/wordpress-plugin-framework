<?php

namespace Hoo\WordPressPluginFramework\Http;

readonly class Url implements UrlInterface
{
	protected function __construct(
		protected Url\Scheme $scheme,
		protected string $host,
		protected int $port,
		protected string $path,
		protected Url\QueryInterface $query,
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
		return new self(
			Url\Scheme::from($scheme),
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

	public function port(): int
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

	public function query(): string
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
			Url\Query::from($query),
		);
	}

	public function queryValue(string $key): string
	{
		return $this->query->value($key);
	}

	public function withQueryValue(string $key, string $value): UrlInterface
	{
		return new self(
			$this->scheme,
			$this->host,
			$this->port,
			$this->path,
			$this->query->withValue($key, $value),
		);
	}

	public function __toString(): string
	{
		$url = "{$this->scheme()}://{$this->host()}:{$this->port()}{$this->path()}";

		$query = $this->query();
		if ($query) {
			$url .= "?{$query}";
		}

		return $url;
	}

	protected static function parse(string $url): array
	{
		$components = parse_url($url);
		if (!$components) {
			//throw new...
		}

		$components['scheme'] = Url\Scheme::from($components['scheme'] ?? '');
		$components['host'] ??= '';
		$components['port'] ??= match ($components['scheme']) {
			Url\Scheme::Http => 80,
			Url\Scheme::Https => 443,
		};
		$components['path'] ??= '';
		$components['query'] = Url\Query::from($components['query'] ?? '');

		return $components;
	}
}