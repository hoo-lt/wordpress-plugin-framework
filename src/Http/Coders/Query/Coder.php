<?php

namespace Hoo\WordPressPluginFramework\Http\Coders\Query;

use Hoo\WordPressPluginFramework\Http\Coders\CoderException;

readonly class Coder implements CoderInterface
{
	public function decode(string $encoded): array
	{
		parse_str($encoded, $decoded);
		return $decoded;
	}

	public function tryDecode(?string $encoded): ?array
	{
		if ($encoded === null) {
			return null;
		}

		parse_str($encoded, $decoded);
		return $decoded;
	}

	public function encode(array|object $decoded): string
	{
		return http_build_query($decoded, '', '&', PHP_QUERY_RFC3986);
	}


	public function tryEncode(array|object|null $decoded): ?string
	{
		if ($decoded === null) {
			return null;
		}

		return http_build_query($decoded, '', '&', PHP_QUERY_RFC3986);
	}
}