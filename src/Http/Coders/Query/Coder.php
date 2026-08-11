<?php

namespace Hoo\WordPressPluginFramework\Http\Coders\Query;

use Hoo\WordPressPluginFramework\{
	Http\Coders\AbstractCoder,
	Http\Coders\CoderException,
};

readonly class Coder extends AbstractCoder implements CoderInterface
{
	public function produces(): array
	{
		return [];
	}

	public function decode(string $encoded): array
	{
		parse_str($encoded, $decoded);
		return $decoded;
	}

	public function encodes(mixed $decoded): bool
	{
		return is_array($decoded) || is_object($decoded);
	}

	public function encode(mixed $decoded): string
	{
		if (!$this->encodes($decoded)) {
			throw new CoderException('failed to encode');
		}

		return http_build_query($decoded, '', '&', PHP_QUERY_RFC3986);
	}
}