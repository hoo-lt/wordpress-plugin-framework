<?php

namespace Hoo\WordPressPluginFramework\Http\Coders\Form;

use Hoo\WordPressPluginFramework\{
	Http\Coders\AbstractCoder,
	Http\Coders\CoderException,
	Http\Coders\CoderInterface,
};

readonly class Coder extends AbstractCoder implements CoderInterface
{
	public function produces(): array
	{
		return [
			$this->mediaTypeFactory->create('application/x-www-form-urlencoded'),
		];
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

		return http_build_query($decoded, '', '&', PHP_QUERY_RFC1738);
	}
}