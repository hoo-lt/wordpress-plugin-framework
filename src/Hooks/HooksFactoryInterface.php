<?php

namespace Hoo\WordPressPluginFramework\Hooks;

use Closure;

interface HooksFactoryInterface
{
	public function create(Closure $hooksBuilderClosure): HooksInterface;
}
