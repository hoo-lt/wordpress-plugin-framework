<?php

namespace Hoo\WordPressPluginFramework\Hooker\Hooks;

interface HookInterface
{
	public function __invoke(): void;
}
