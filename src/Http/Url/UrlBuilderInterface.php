<?php

namespace Hoo\WordPressPluginFramework\Http\Url;

use stdClass;

interface UrlBuilderInterface
{
	public function withScheme(string $scheme): static;

	public function withHost(string $host): static;

	public function withPort(int $port): static;
	public function withoutPort(): static;

	public function withPath(string $path): static;

	public function withQuery(array|stdClass $query): static;
	public function withUnnormalizedQuery(mixed $query): static;

	public function build(): UrlInterface;
}
