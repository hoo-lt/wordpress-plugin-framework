<?php

namespace Hoo\WordPressPluginFramework\Http\Coders\Json;

use Hoo\WordPressPluginFramework\Http\Coders\CoderException;
use Throwable;

readonly class Coder implements CoderInterface
{
	public function supports(string $mediaType): bool
	{
		[
			$type,
			$subtype
		] = explode('/', strtolower($mediaType), 2);

		if ($type !== 'application') {
			return false;
		}

		if (
			$subtype !== 'json' &&
			!str_ends_with($subtype, '+json')
		) {
			return false;
		}

		return true;
	}

	public function decodes(mixed $encoded): bool
	{
		return is_string($encoded);
	}

	public function decode(mixed $encoded): mixed
	{
		if (!$this->decodes($encoded)) {
			throw new CoderException('failed to decode');
		}

		try {
			return json_decode($encoded, true, 512, JSON_THROW_ON_ERROR);
		} catch (Throwable $throwable) {
			throw new CoderException($throwable->getMessage());
		}
	}

	public function tryDecode(mixed $encoded): mixed
	{
		if (!$this->decodes($encoded)) {
			return null;
		}

		try {
			return json_decode($encoded, true, 512, JSON_THROW_ON_ERROR);
		} catch (Throwable $throwable) {
			return null;
		}
	}

	public function encodes(mixed $decoded): bool
	{
		return !is_resource($decoded);
	}

	public function encode(mixed $decoded): string
	{
		if (!$this->encodes($decoded)) {
			throw new CoderException('failed to encode');
		}

		try {
			return json_encode($decoded, JSON_THROW_ON_ERROR, 512);
		} catch (Throwable $throwable) {
			throw new CoderException($throwable->getMessage());
		}
	}

	public function tryEncode(mixed $decoded): ?string
	{
		if (!$this->encodes($decoded)) {
			return null;
		}

		try {
			return json_encode($decoded, JSON_THROW_ON_ERROR, 512);
		} catch (Throwable $throwable) {
			return null;
		}
	}
}