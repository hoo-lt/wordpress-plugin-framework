<?php

namespace Hoo\WordPressPluginFramework\View;

interface ViewInterface
{
	public function withValue(string $key, mixed $value): ViewInterface;
	public function __invoke(string $view): string;
}