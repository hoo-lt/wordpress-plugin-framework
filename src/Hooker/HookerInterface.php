<?php

namespace Hoo\WordPressPluginFramework\Hooker;

use Hoo\WordPressPluginFramework\Hooker\Hooks\HookInterface;

interface HookerInterface
{
	public function withHooks(HookInterface ...$hooks): HookerInterface;

	public function __invoke(): void;
}
