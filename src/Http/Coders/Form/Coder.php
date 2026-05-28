<?php

namespace Hoo\WordPressPluginFramework\Http\Coders\Form;

use Hoo\WordPressPluginFramework\Http\Coders\CoderException;

readonly class Coder implements CoderInterface
{
	public function decode(string $string): array
	{
		$array = $this->tryDecode($string);
		if ($array === null) {
			throw new CoderException('failed to decode');
		}

		return $array;
	}

	public function tryDecode(string $string): ?array
	{
		$array = [];

		parse_str($string, $array);

		return $array;
	}

	public function encode(array $array): string
	{
		$string = $this->tryEncode($array);
		if ($string === null) {
			throw new CoderException('failed to encode');
		}

		return $string;
	}

	public function tryEncode(array $array): ?string
	{
		return http_build_query($array, '', '&', PHP_QUERY_RFC1738);

	}
}