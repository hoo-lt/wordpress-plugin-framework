<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameter;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\QuotedString\QuotedStringFactoryInterface,
	Http\Semantics\QuotedString\QuotedStringInterface,
	Http\Semantics\Token\Token,
	Http\Semantics\Token\TokenInterface,
};

readonly class ParameterFactory implements ParameterFactoryInterface
{
	public function __construct(
		protected QuotedStringFactoryInterface $quotedStringFactory,
	) {
	}

	public function create(string $parameter): ParameterInterface
	{
		[
			$name,
			$value,
		] = array_pad(explode('=', $parameter, 2), 2, '');

		return new Parameter(
			$this->createName($name),
			$this->createValue($value),
		);
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
			return $this->quotedStringFactory->create($value);
		}

		return new Token($value);
	}
}
