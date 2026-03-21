<?php

namespace Hoo\WordPressPluginFramework\Http;

interface UrlInterface
{
	public function scheme(): string;
	public function withScheme(string $scheme): self;
	public function host(): string;
	public function withHost(string $host): self;
	public function port(): ?int;
	public function withPort(int $port): self;
	public function withoutPort(): self;
	public function path(): string;
	public function withPath(string $path): self;
	public function query(): ?string;
	public function withQuery(string $query): self;
	public function withoutQuery(): self;
	public function __toString(): string;
}