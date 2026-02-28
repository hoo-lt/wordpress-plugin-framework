<?php

namespace Hoo\WordPressPluginFramework\View;

interface ViewInterface
{
	public function __invoke(string $view, array $array): string;
}