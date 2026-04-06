<?php

namespace Hoo\WordPressPluginFramework\Database\Migrator;

interface MigratorInterface
{
	public function migrate(): void;
}
