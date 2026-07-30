<?php

namespace Hoo\WordPressPluginFramework\Hooks;

interface HookInterface
{
	public function __invoke(): void;
}
