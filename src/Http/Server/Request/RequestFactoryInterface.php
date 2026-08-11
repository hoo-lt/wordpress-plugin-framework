<?php

namespace Hoo\WordPressPluginFramework\Http\Server\Request;

interface RequestFactoryInterface
{
	public function create(): RequestInterface;
}
