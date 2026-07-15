<?php

namespace Hoo\WordPressPluginFramework\Http\Coders\Json;

use Hoo\WordPressPluginFramework\{
	Http\Coders\AbstractCoder,
	Http\Coders\CoderException,
	Http\Coders\CoderInterface,
	Http\Semantics\ContentType\MediaType\MediaType,
	Http\Semantics\ContentType\MediaType\MediaTypeInterface,
};
use Throwable;

readonly class Coder extends AbstractCoder implements CoderInterface
{
	public function mediaTypes(): array
	{
		return [
			new MediaType('application', 'json'),
		];
	}

	public function codes(MediaTypeInterface $mediaType): bool
	{
		if (!parent::codes($mediaType)) {
			return false;
		}

		return $mediaType->charset() === 'utf-8';
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
			return json_decode($encoded, false, 512, JSON_THROW_ON_ERROR);
		} catch (Throwable $throwable) {
			throw new CoderException($throwable->getMessage());
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
}
