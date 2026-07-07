<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\QuotedString;

interface QuotedStringFactoryInterface
{
	public function create(string $quotedString): QuotedStringInterface;
}
