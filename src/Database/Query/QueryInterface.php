<?php

namespace Hoo\WordpressPluginFramework\Database\Query;

interface QueryInterface
{
	public function __invoke(): string;
}