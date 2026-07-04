<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Parameters;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\Parameters\Parameter\Parameter,
	Http\Semantics\Parameters\Parameter\ParameterFactoryInterface,
};

readonly class ParametersFactory implements ParametersFactoryInterface
{
	public function __construct(
		protected ParameterFactoryInterface $parameterFactory,
	) {
	}

	public function create(string $parameters): array
	{
		$this->validate($parameters);

		preg_match_all('/' . Parameter::PATTERN . '/', $parameters, $matches);

		return array_map($this->parameterFactory->create(...), $matches[0]);
	}

	public function tryCreate(?string $parameters): array
	{
		if ($parameters === null) {
			return [];
		}

		return $this->create($parameters);
	}

	/**
	 * the tail of: parameters = *( OWS ";" OWS [ parameter ] ) per RFC 9110 section 5.6.6,
	 * as received after the leading ";" split
	 */
	protected function validate(string $parameters): void
	{
		$parameter = '(?:' . Parameter::PATTERN . ')?+';
		$pattern = '/\A[ \t]*+' . $parameter . '[ \t]*+(?:;[ \t]*+' . $parameter . '[ \t]*+)*+\z/';

		if (preg_match($pattern, $parameters) !== 1) {
			throw new ParametersFactoryException('not a valid parameters section');
		}
	}
}
