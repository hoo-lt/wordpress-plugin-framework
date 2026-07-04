<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameter;

interface ParameterFactoryInterface
{
	public function create(string $parameter): ParameterInterface;
	public function tryCreate(?string $parameter): ?ParameterInterface;
}
