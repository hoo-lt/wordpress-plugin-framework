<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Token;

use Stringable;

interface TokenInterface extends Stringable
{
	public function value(): string;
}