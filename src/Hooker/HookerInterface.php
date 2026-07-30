<?php

namespace Hoo\WordPressPluginFramework\Hooker;

interface HookerInterface
{
	public function __invoke(): void;
}
