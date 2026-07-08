<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Parameters;

use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameter\ParameterFactoryInterface;

readonly class ParametersFactory implements ParametersFactoryInterface
{
	public function __construct(
		protected ParameterFactoryInterface $parameterFactory,
	) {
	}

	public function create(string $parameters): ParametersInterface
	{
		return new Parameters(
			array_map(
				$this->parameterFactory->create(...),
				$this->parameters($parameters),
			),
		);
	}

	protected function parameters(string $parameters): array
	{
		preg_match_all('/(?:"(?:\\\\.|[^"\\\\])*+"|[^;])++/', $parameters, $matches);

		return array_values(array_filter(array_map(fn($element) => trim($element, " \t"), $matches[0]), fn($element) => $element !== ''));
	}
}
