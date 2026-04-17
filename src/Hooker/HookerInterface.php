<?php

namespace Hoo\WordPressPluginFramework\Hooker;

use Hoo\WordPressPluginFramework\Hook\HookInterface;

interface HookerInterface
{
	public function withHooks(HookInterface ...$hooks): static;

	public function __invoke(): void;
}
