<?php

namespace Hoo\WordPressPluginFramework\Repositories\Database\Migrator;

interface RepositoryInterface
{
	public function has(string $hash): bool;
	public function add(string $hash): void;
	public function remove(string $hash): void;
}
