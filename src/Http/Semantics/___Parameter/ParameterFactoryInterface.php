<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Parameter;

interface ParameterFactoryInterface
{
	public function create(string $parameter): ParameterInterface;
}
