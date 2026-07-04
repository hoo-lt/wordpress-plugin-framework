<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameter;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\QuotedString\QuotedString,
	Http\Semantics\QuotedString\QuotedStringInterface,
	Http\Semantics\Token\Token,
	Http\Semantics\Token\TokenInterface,
};

readonly class Parameter implements ParameterInterface
{
	/**
	 * parameter = parameter-name "=" parameter-value per RFC 9110 section 5.6.6
	 *
	 * parameter-name = token
	 * parameter-value = ( token / quoted-string )
	 */
	public const PATTERN = '(?:' . Token::PATTERN . ')=(?:' . Token::PATTERN . '|' . QuotedString::PATTERN . ')';

	public function __construct(
		protected TokenInterface $name,
		protected TokenInterface|QuotedStringInterface $value,
	) {
	}

	public function name(): string
	{
		return $this->name->value();
	}

	public function value(): string
	{
		return $this->value->value();
	}

	public function __toString(): string
	{
		return "{$this->name}={$this->value}";
	}
}
