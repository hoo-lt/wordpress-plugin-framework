<?php

namespace Hoo\WordPressPluginFramework\Http\Url;

use Hoo\WordPressPluginFramework\Http\Url\Scheme\Scheme;

interface UrlFactoryInterface
{
	public function from(Scheme $scheme, string $host, ?int $port, string $path, array $query): UrlInterface;
	public function fromUrl(string $url): UrlInterface;
	public function fromServer(array $server): UrlInterface;
}
