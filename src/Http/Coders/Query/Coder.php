<?php

namespace Hoo\WordPressPluginFramework\Http\Coders\Query;

use Hoo\WordPressPluginFramework\Http\Coders\CoderException;

readonly class Coder implements CoderInterface
{
	public function supports(string $mediaType): bool
	{
		[
			$type,
			$subtype
		] = explode('/', strtolower($mediaType), 2);

		return false;
	}

	public function decodes(mixed $encoded): bool
	{
		return is_string($encoded);
	}

	public function decode(mixed $encoded): array
	{
		if (!$this->decodes($encoded)) {
			throw new CoderException('failed to decode');
		}

		parse_str($encoded, $decoded);
		return $decoded;
	}

	public function tryDecode(mixed $encoded): ?array
	{
		if (!$this->decodes($encoded)) {
			return null;
		}

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

		return http_build_query($decoded, '', '&', PHP_QUERY_RFC3986);
	}

	public function tryEncode(mixed $decoded): ?string
	{
		if (!$this->encodes($decoded)) {
			return null;
		}

		return http_build_query($decoded, '', '&', PHP_QUERY_RFC3986);
	}
}