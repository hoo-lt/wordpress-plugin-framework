<?php

namespace Hoo\WordPressPluginFramework\Http\Coders\Form;

use Hoo\WordPressPluginFramework\Http;

readonly class Coder implements Http\Coders\CoderInterface
{
	public function decode(string $string): mixed
	{
		$mixed = [];

		parse_str($string, $mixed);

		return $mixed;
	}

	public function encode(mixed $mixed): string
	{
		return http_build_query($mixed, '', '&', PHP_QUERY_RFC1738);
	}
}