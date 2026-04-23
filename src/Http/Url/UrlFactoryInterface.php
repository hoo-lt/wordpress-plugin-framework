<?php

namespace Hoo\WordPressPluginFramework\Http\Url;

interface UrlFactoryInterface
{
	public function from(string $scheme, string $host, ?int $port, string $path, array $query): UrlInterface;
	public function fromUrl(string $url): UrlInterface;
	public function fromServer(array $server): UrlInterface;
}
