<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Values;

interface ValuesFactoryInterface
{
	public function body(string $key): ValuesInterface;
	public function query(string $key): ValuesInterface;
}
