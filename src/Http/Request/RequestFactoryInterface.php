<?php

namespace Hoo\WordPressPluginFramework\Http\Request;

interface RequestFactoryInterface
{
	public function create(): RequestInterface;
}
