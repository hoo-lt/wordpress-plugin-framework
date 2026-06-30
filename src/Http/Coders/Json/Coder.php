<?php

namespace Hoo\WordPressPluginFramework\Http\Coders\Json;

use Hoo\WordPressPluginFramework\Http\Coders\CoderException;
use Throwable;

readonly class Coder implements CoderInterface
{
	public function decode(string $encoded): array
	{
		try {
			return json_decode($encoded, true, 512, JSON_THROW_ON_ERROR);
		} catch (Throwable $throwable) {
			throw new CoderException($throwable->getMessage());
		}
	}

	public function tryDecode(?string $encoded): ?array
	{
		if ($encoded === null) {
			return null;
		}

		try {
			return json_decode($encoded, true, 512, JSON_THROW_ON_ERROR);
		} catch (Throwable $throwable) {
			return null;
		}
	}

	public function encode(array|object $decoded): string
	{
		try {
			return json_encode($decoded, JSON_THROW_ON_ERROR, 512);
		} catch (Throwable $throwable) {
			throw new CoderException($throwable->getMessage());
		}
	}

	public function tryEncode(array|object|null $decoded): ?string
	{
		if ($decoded === null) {
			return null;
		}

		try {
			return json_encode($decoded, JSON_THROW_ON_ERROR, 512);
		} catch (Throwable $throwable) {
			return null;
		}
	}
}