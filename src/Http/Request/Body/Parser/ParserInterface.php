<?php

namespace Hoo\WordPressPluginFramework\Http\Request\Body\Parser;

interface ParserInterface
{
	public function __invoke(): ?array;
}
