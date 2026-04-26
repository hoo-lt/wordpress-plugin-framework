<?php

namespace Hoo\WordPressPluginFramework\Http\Coders;

interface DecoderInterface
{
	public function decode(string $string): mixed;
}