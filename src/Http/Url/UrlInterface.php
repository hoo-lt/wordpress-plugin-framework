<?php

namespace Hoo\WordPressPluginFramework\Http\Url;

use Hoo\WordPressPluginFramework\Http;

interface UrlInterface
{
	public function scheme(): Http\Url\Scheme\Scheme;
	public function withScheme(Http\Url\Scheme\Scheme $scheme): static;

	public function host(): string;
	public function withHost(string $host): static;

	public function port(): ?int;
	public function withPort(int $port): static;
	public function withoutPort(): static;

	public function path(): string;
	public function withPath(string $path): static;

	public function query(): ?Http\Url\Query\QueryInterface;
	public function withQuery(Http\Url\Query\QueryInterface $query): static;
	public function withoutQuery(): static;

	public function __toString(): string;
}