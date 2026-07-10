<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\MediaRanges\MediaRange;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\Parameters\Parameter\ParameterInterface,
	Http\Semantics\Parameters\ParametersInterface,
	Http\Semantics\Weight\WeightInterface,
};

interface MediaRangeInterface
{
	public function type(): string;
	public function subtype(): string;
	public function parameters(): ParametersInterface;
	public function parameter(string $name): ?ParameterInterface;
	public function weight(): ?WeightInterface;
}
