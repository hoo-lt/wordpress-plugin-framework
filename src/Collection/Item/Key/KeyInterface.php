<?php

namespace Hoo\WordpressPluginFramework\Collection\Item\Key;

interface KeyInterface
{
	public function __invoke(): int|string;
}