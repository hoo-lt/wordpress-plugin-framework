<?php

namespace Hoo\WordPressPluginFramework\Http\Url;

use Hoo\WordPressPluginFramework\{
	Http\Url\Query\QueryFactoryInterface,
	Http\Url\Query\QueryInterface,
	Http\Url\Scheme\Scheme,
};
use stdClass;

readonly class UrlBuilder implements UrlBuilderInterface
{
	protected QueryInterface $query;

	public function __construct(
		protected QueryFactoryInterface $queryFactory,
		protected ?Scheme $scheme = null,
		protected string $host = '',
		protected ?int $port = null,
		protected string $path = '',
		?QueryInterface $query = null,
	) {
		$this->query = $query ?? $queryFactory->create([]);
	}

	public function withScheme(Scheme|string $scheme): static
	{
		if (!$scheme instanceof Scheme) {
			$scheme = Scheme::create($scheme);
		}

		return new static($this->queryFactory, $scheme, $this->host, $this->port, $this->path, $this->query);
	}

	public function withHost(string $host): static
	{
		return new static($this->queryFactory, $this->scheme, $host, $this->port, $this->path, $this->query);
	}

	public function withPort(int $port): static
	{
		return new static($this->queryFactory, $this->scheme, $this->host, $port, $this->path, $this->query);
	}

	public function withoutPort(): static
	{
		return new static($this->queryFactory, $this->scheme, $this->host, null, $this->path, $this->query);
	}

	public function withPath(string $path): static
	{
		return new static($this->queryFactory, $this->scheme, $this->host, $this->port, $path, $this->query);
	}

	public function withQuery(QueryInterface|array|stdClass $query): static
	{
		if (!$query instanceof QueryInterface) {
			$query = $this->queryFactory->create($query);
		}

		return new static($this->queryFactory, $this->scheme, $this->host, $this->port, $this->path, $query);
	}

	public function withUnnormalizedQuery(mixed $query): static
	{
		$query = $this->queryFactory->createFromUnnormalized($query);

		return new static($this->queryFactory, $this->scheme, $this->host, $this->port, $this->path, $query);
	}

	public function build(): UrlInterface
	{
		if ($this->scheme === null) {
			throw new UrlBuilderException('scheme is mandatory');
		}

		if ($this->host === '') {
			throw new UrlBuilderException('host is mandatory');
		}

		return new Url($this->scheme, $this->host, $this->port, $this->path, $this->query);
	}
}
