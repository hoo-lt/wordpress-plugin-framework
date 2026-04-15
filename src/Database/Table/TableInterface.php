<?php

namespace Hoo\WordPressPluginFramework\Database\Table;

interface TableInterface
{
	public function __invoke(string $table): string;
}
