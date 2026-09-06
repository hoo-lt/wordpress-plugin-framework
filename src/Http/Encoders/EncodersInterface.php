<?php

namespace Hoo\WordPressPluginFramework\Http\Encoders;

interface EncodersInterface
{
	public function get(string $contentType, mixed $encoded): EncoderInterface;
}
