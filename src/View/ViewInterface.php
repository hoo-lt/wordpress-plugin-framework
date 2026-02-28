<?php

namespace Hoo\WordpressPluginFramework\View;

interface ViewInterface
{
	public function __invoke(string $view, array $array): string;
}