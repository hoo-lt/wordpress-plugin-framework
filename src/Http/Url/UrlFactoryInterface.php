<?php

namespace Hoo\WordPressPluginFramework\Http\Url;

interface UrlFactoryInterface
{
	public function create(string $url): UrlInterface;
}
