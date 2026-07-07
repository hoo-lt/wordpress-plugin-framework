<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameter;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\QuotedString\QuotedStringInterface,
	Http\Semantics\Token\TokenInterface,
};

interface ParameterInterface
{
	public function name(): TokenInterface;
	public function value(): TokenInterface|QuotedStringInterface;
}
