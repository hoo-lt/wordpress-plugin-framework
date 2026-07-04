<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameter;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\QuotedString\QuotedString,
	Http\Semantics\QuotedString\QuotedStringInterface,
	Http\Semantics\Token\Token,
	Http\Semantics\Token\TokenInterface,
};

readonly class ParameterFactory implements ParameterFactoryInterface
{
	public function create(string $parameter): ParameterInterface
	{
		$this->validate($parameter);

		[
			$name,
			$value,
		] = explode('=', $parameter, 2);

		return new Parameter(
			$this->createName($name),
			$this->createValue($value),
		);
	}

	public function tryCreate(?string $parameter): ?ParameterInterface
	{
		if ($parameter === null) {
			return null;
		}

		return $this->create($parameter);
	}

	protected function validate(string $parameter): void
	{
		if (preg_match('/\A' . Parameter::PATTERN . '\z/', $parameter) !== 1) {
			throw new ParameterFactoryException('not a valid parameter');
		}
	}

	protected function createName(string $name): TokenInterface
	{
		return new Token(
			strtolower($name),
		);
	}

	protected function createValue(string $value): TokenInterface|QuotedStringInterface
	{
		if (str_starts_with($value, '"')) {
			return new QuotedString($value);
		}

		return new Token($value);
	}
}
