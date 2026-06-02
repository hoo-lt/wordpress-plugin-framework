<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Values;

readonly class ValuesFactory implements ValuesFactoryInterface
{
	public function body(string $key): ValuesInterface
	{
		return new Body\Values($key);
	}

	public function query(string $key): ValuesInterface
	{
		return new Query\Values($key);
	}
}
