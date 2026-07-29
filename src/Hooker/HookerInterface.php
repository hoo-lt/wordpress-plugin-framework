<?php

namespace Hoo\WordPressPluginFramework\Hooker;

interface HookerInterface
{
	public function hooks(): array;

	public function __invoke(): void;
}
