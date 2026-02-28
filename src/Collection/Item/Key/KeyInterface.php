<?php

namespace Hoo\WordPressPluginFramework\Collection\Item\Key;

interface KeyInterface
{
	public function __invoke(): int|string;
}