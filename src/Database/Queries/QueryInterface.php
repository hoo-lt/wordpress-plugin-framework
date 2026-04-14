<?php

namespace Hoo\WordPressPluginFramework\Database\Queries;

interface QueryInterface
{
	public function __invoke(): string;
}