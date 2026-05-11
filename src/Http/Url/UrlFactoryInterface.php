<?php

namespace Hoo\WordPressPluginFramework\Http\Url;

interface UrlFactoryInterface
{
	public function from(string $url): UrlInterface;
	public function fromServer(): UrlInterface;
}
