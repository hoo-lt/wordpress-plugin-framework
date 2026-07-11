<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Parameter;

interface ParameterInterface
{
	public function name(): string;
	public function value(): string;
}
