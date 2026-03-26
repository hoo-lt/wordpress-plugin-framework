<?php

namespace Hoo\WordPressPluginFramework\Json;

use Throwable;

class Json implements JsonInterface
{
	public function decode(string $json): array
	{
		try {
			return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
		} catch (Throwable $throwable) {
			throw new JsonException($throwable->getMessage());
		}
	}

	public function encode(array $json): string
	{
		try {
			return json_encode($json, JSON_THROW_ON_ERROR, 512);
		} catch (Throwable $throwable) {
			throw new JsonException($throwable->getMessage());
		}
	}
}