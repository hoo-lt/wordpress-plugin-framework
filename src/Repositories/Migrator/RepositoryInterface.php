<?php

namespace Hoo\WordPressPluginFramework\Repositories\Migrator;

interface RepositoryInterface
{
	public function has(string $hash): bool;
	public function add(string $hash): void;
	public function remove(string $hash): void;
}
