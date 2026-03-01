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
			...self::parseUrl($url)
		);
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

	public function withQuery(array $query): UrlInterface
	{
		return new self(
			$this->scheme,
			$this->host,
			$this->port,
			$this->path,
			Url\Query::from($query),
		);
	}

	public function withQueryValue(string $name, string $value): UrlInterface
	{
		return new self(
			$this->scheme,
			$this->host,
			$this->port,
			$this->path,
			$this->query->withValue($name, $value),
		);
	}

	public function __toString(): string
	{
		return "{$this->scheme->value}://{$this->host}{$this->path}?{$this->query}";
	}

	protected static function parseUrl(string $url): array
	{
		$components = parse_url($url);
		if (!$components) {
			//throw new...
		}

		$components['scheme'] = Url\Scheme::from($components['scheme']);
		$components['host'] ??= '';
		$components['port'] ??= match ($components['scheme']) {
			Url\Scheme::Http => 80,
			Url\Scheme::Https => 443,
		};
		$components['path'] ??= '';
		$components['query'] ??= '';

		parse_str($components['query'], $components['query']);

		$components['query'] = Url\Query::from($components['query']);

		return $components;
	}
}