<?php

namespace Hoo\WordPressPluginFramework\Hooker\Hooks;

use Closure;

interface HookFactoryInterface
{
	public function action(string $name, Closure $closure, int $priority = 10): HookInterface;

	public function filter(string $name, Closure $closure, int $priority = 10): HookInterface;

	public function activation(string $file, Closure $closure): HookInterface;

	public function deactivation(string $file, Closure $closure): HookInterface;
}
