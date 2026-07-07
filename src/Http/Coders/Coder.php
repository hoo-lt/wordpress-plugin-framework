<?php

namespace Hoo\WordPressPluginFramework\Http\Coders;

use Hoo\WordPressPluginFramework\{
	Http\Coders\AbstractCoder,
	Http\Coders\CoderException,
};

readonly class Coder extends AbstractCoder implements CoderInterface
{
	public function __construct(
		protected array $mediaTypes,
	) {
	}

	public function mediaTypes(): array
	{
		return array_map($this->mediaTypeFactory->create(...), $this->mediaTypes);
	}

	public function decodes(mixed $encoded): bool
	{
		return is_string($encoded);
	}

	public function decode(mixed $encoded): string
	{
		if (!$this->decodes($encoded)) {
			throw new CoderException('failed to decode');
		}

		return $encoded;
	}

	public function encodes(mixed $decoded): bool
	{
		return is_string($decoded);
	}

	public function encode(mixed $decoded): string
	{
		if (!$this->encodes($decoded)) {
			throw new CoderException('failed to encode');
		}

		return $decoded;
	}
}