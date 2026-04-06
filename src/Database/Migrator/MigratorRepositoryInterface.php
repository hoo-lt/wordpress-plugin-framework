<?php

namespace Hoo\WordPressPluginFramework\Database\Migrator;

interface MigratorRepositoryInterface
{
	public function applied(): array;
	public function apply(string $name): void;
}
