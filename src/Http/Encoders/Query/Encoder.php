<?php

namespace Hoo\WordPressPluginFramework\Http\Encoders\Query;

use Hoo\WordPressPluginFramework\Http\Encoders\EncoderException;
use stdClass;

readonly class Encoder implements EncoderInterface
{
	public function encode(mixed $decoded): string
	{
		if (!$this->encodesType($decoded)) {
			throw new EncoderException('does not encode');
		}

		return http_build_query($decoded, '', '&', PHP_QUERY_RFC3986);
	}

	protected function encodesType(mixed $decoded): bool
	{
		if (!is_array($decoded) && !$decoded instanceof stdClass) {
			return false;
		}

		return true;
	}
}
