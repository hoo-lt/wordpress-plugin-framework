<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Parameters;

interface ParametersFactoryInterface
{
	/**
	 * @return Parameter\ParameterInterface[]
	 */
	public function create(string $parameters): array;

	/**
	 * @return Parameter\ParameterInterface[]
	 */
	public function tryCreate(?string $parameters): array;
}
