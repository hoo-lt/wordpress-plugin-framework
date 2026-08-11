<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\ContentType\MediaType;

use Stringable;
use Traversable;

interface MediaTypeInterface extends Stringable
{
	public function type(): string;
	public function subtype(): string;

	public function parameters(): Traversable;
	public function parameter(string $name): ?string;

	public function charset(): ?string;
}
