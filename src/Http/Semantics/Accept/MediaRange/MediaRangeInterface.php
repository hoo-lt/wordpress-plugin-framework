<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Accept\MediaRange;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\Accept\MediaRange\Precedence\Precedence,
	Http\Semantics\ContentType\MediaType\MediaTypeInterface,
};

interface MediaRangeInterface
{
	public function type(): string;
	public function subtype(): string;

	public function parameters(): array;
	public function parameter(string $name): ?string;

	public function charset(): ?string;

	public function q(): float;

	public function mediaType(): ?MediaTypeInterface;

	public function precedence(MediaTypeInterface $mediaType): ?Precedence;
}
