<?php

namespace Hoo\WordPressPluginFramework\Database\Migrator;

interface MigratorInterface
{
	public function __invoke(): void;
}
