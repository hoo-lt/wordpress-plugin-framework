<?php

namespace Hoo\WordPressPluginFramework\Http\Coders\Json;

use Hoo\WordPressPluginFramework\{
	Http,
	Json,
};

readonly class Coder implements Http\Coders\CoderInterface
{
	public function __construct(
		protected Json\JsonInterface $json,
	) {
	}

	public function decode(string $string): mixed
	{
		return $this->json->decode($string);
	}

	public function encode(mixed $mixed): string
	{
		return $this->json->encode($mixed);
	}
}