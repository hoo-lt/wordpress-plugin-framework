<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers\Accept\MediaRange;

use Hoo\WordPressPluginFramework\{
	Http\Message\Headers\Accept\MediaRange\Precedence\Precedence,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
};

interface MediaRangeInterface
{
	public function type(): string;
	public function subtype(): string;

	public function q(): float;

	public function mediaType(): ?MediaTypeInterface;

	public function precedence(MediaTypeInterface $mediaType): ?Precedence;
}
