<?php

namespace Hoo\WordPressPluginFramework\Http\Url;

use Hoo\WordPressPluginFramework\Http\Url\{
	Scheme\Scheme,
	Query\QueryInterface
};

interface UrlInterface
{
	public function scheme(): Scheme;
	public function withScheme(Scheme $scheme): static;

	public function host(): string;
	public function withHost(string $host): static;

	public function port(): ?int;
	public function withPort(int $port): static;
	public function withoutPort(): static;

	public function path(): string;
	public function withPath(string $path): static;

	public function query(): QueryInterface;
	public function withQuery(QueryInterface $query): static;
	public function withoutQuery(): static;

	public function queryValue(string $key): mixed;
	public function withQueryValue(string $key, mixed $value): static;
	public function withoutQueryValue(string $key): static;

	public function __toString(): string;
}