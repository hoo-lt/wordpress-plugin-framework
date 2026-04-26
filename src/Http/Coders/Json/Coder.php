<?php

namespace Hoo\WordPressPluginFramework\Http\Coders\Json;

use Hoo\WordPressPluginFramework\Json\JsonInterface;

readonly class Coder implements CoderInterface
{
	public function __construct(
		protected JsonInterface $json,
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