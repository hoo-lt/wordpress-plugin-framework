<?php

namespace Hoo\WordPressPluginFramework\Http;

interface UrlInterface
{
	public function scheme(): string;
	public function withScheme(string $scheme): UrlInterface;
	public function host(): string;
	public function withHost(string $host): UrlInterface;
	public function port(): ?int;
	public function withPort(int $port): UrlInterface;
	public function withoutPort(): UrlInterface;
	public function path(): string;
	public function withPath(string $path): UrlInterface;
	public function query(): ?string;
	public function withQuery(string $query): UrlInterface;
	public function withoutQuery(): UrlInterface;
	public function __toString(): string;
}