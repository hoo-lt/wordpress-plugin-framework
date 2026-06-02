<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Values;

readonly class ValuesFactory
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
