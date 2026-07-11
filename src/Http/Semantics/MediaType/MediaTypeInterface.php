<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\MediaType;

use Hoo\WordPressPluginFramework\Http\Semantics\Parameter\ParameterInterface;

interface MediaTypeInterface
{
	public function type(): string;
	public function subtype(): string;
	
	public function parameters(): array;
	public function parameter(string $name): ?ParameterInterface;
}
