<?php

namespace Hoo\WordPressPluginFramework\Http\Decoders\Query;

readonly class Decoder implements DecoderInterface
{
	public function decode(string $encoded): mixed
	{
		parse_str($encoded, $decoded);
		return $decoded;
	}
}
