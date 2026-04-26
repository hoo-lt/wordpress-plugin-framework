<?php

namespace Hoo\WordPressPluginFramework\Http\Coders;

interface EncoderInterface
{
	public function encode(mixed $mixed): string;
}