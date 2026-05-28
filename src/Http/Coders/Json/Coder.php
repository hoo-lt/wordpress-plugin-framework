<?php

namespace Hoo\WordPressPluginFramework\Http\Coders\Json;

use Hoo\WordPressPluginFramework\Http\Coders\CoderException;
use Throwable;

readonly class Coder implements CoderInterface
{
	public function decode(string $string): array
	{
		try {
			return json_decode($string, true, 512, JSON_THROW_ON_ERROR);
		} catch (Throwable $throwable) {
			throw new CoderException($throwable->getMessage());
		}
	}

	public function tryDecode(string $string): ?array
	{
		try {
			return json_decode($string, true, 512, JSON_THROW_ON_ERROR);
		} catch (Throwable $throwable) {
			return null;
		}
	}

	public function encode(array $array): string
	{
		try {
			return json_encode($array, JSON_THROW_ON_ERROR, 512);
		} catch (Throwable $throwable) {
			throw new CoderException($throwable->getMessage());
		}
	}

	public function tryEncode(array $array): ?string
	{
		try {
			return json_encode($array, JSON_THROW_ON_ERROR, 512);
		} catch (Throwable $throwable) {
			return null;
		}
	}
}