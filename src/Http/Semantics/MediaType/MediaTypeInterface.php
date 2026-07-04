<?php

namespace Hoo\WordPressPluginFramework\Http\Coders\MediaType;

use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameter\ParameterInterface;
use Stringable;

interface MediaTypeInterface extends Stringable
{
	public function type(): string;
	public function subtype(): string;

	public function parameters(): array;
	public function parameter(string $name): ?ParameterInterface;

	public function charset(): ?string;
}
