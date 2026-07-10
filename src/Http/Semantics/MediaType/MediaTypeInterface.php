<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\MediaType;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\Parameters\Parameter\ParameterInterface,
	Http\Semantics\Parameters\ParametersInterface,
};

interface MediaTypeInterface
{
	public function type(): string;
	public function subtype(): string;
	public function parameters(): ParametersInterface;
	public function parameter(string $name): ?ParameterInterface;
}
