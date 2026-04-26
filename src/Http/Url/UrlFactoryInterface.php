<?php

namespace Hoo\WordPressPluginFramework\Http\Url;

interface UrlFactoryInterface
{
	public function fromUrl(string $url): UrlInterface;
	public function fromServer(array $server): UrlInterface;
}
