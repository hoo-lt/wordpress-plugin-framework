<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Parameters;

interface ParametersFactoryInterface
{
	public function create(string $parameters): ParametersInterface;
}
