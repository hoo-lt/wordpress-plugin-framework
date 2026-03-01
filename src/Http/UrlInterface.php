<?php

namespace Hoo\WordPressPluginFramework\Http;

interface UrlInterface
{
	public static function from(string $url): self;
	public function withScheme(string $scheme): self;
	public function withHost(string $host): self;
	public function withPort(int $port): self;
	public function withPath(string $path): self;
	public function withQuery(array $query): self;
	public function withQueryValue(string $name, string $value): self;
	public function __toString(): string;
}