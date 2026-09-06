<?php

namespace Hoo\WordPressPluginFramework\Http\Decoders;

interface DecodersInterface
{
	public function get(string $contentType, mixed $encoded): DecoderInterface;
}
