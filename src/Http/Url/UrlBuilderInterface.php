<?php

namespace Hoo\WordPressPluginFramework\Http\Url;

use Hoo\WordPressPluginFramework\{
	Http\Url\Query\QueryInterface,
	Http\Url\Scheme\Scheme,
};
use stdClass;

interface UrlBuilderInterface
{
	public function withScheme(Scheme|string $scheme): static;

	public function withHost(string $host): static;

	public function withPort(int $port): static;
	public function withoutPort(): static;

	public function withPath(string $path): static;

	public function withQuery(QueryInterface|array|stdClass $query): static;
	public function withUnnormalizedQuery(mixed $query): static;

	public function build(): UrlInterface;
}
