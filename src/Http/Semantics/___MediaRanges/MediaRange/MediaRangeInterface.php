<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\MediaRanges\MediaRange;

use Hoo\WordPressPluginFramework\Http\Semantics\Parameter\ParameterInterface;

interface MediaRangeInterface
{
	public function type(): string;
	public function subtype(): string;

	public function parameters(): array;
	public function parameter(string $name): ?ParameterInterface;

	public function q(): float;
}
