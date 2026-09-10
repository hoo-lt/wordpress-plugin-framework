<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers\Accept\MediaRange;

use Hoo\WordPressPluginFramework\{
	Http\Message\Headers\Accept\MediaRange\Precedence\Precedence,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
	Http\Message\Headers\Parameters\ParametersInterface,
};
use Closure;

interface MediaRangeInterface
{
	public function type(): string;
	public function withType(string $type): static;

	public function subtype(): string;
	public function withSubtype(string $subtype): static;

	public function parameters(): ParametersInterface;
	public function withParameters(ParametersInterface|Closure $parameters): static;

	public function q(): float;
	public function withQ(string $q): static;

	public function mediaType(): ?MediaTypeInterface;

	public function precedence(MediaTypeInterface $mediaType): ?Precedence;
}
