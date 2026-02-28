<?php

namespace Hoo\WordPressPluginFramework\Database\Query;

interface QueryInterface
{
	public function __invoke(): string;
}