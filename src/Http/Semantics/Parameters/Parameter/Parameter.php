<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameter;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\QuotedString\QuotedStringInterface,
	Http\Semantics\Token\TokenInterface,
};

readonly class Parameter implements ParameterInterface
{
	public function __construct(
		protected TokenInterface $name,
		protected TokenInterface|QuotedStringInterface $value,
	) {
	}

	public function name(): TokenInterface
	{
		return $this->name;
	}

	public function value(): TokenInterface|QuotedStringInterface
	{
		return $this->value;
	}
}
