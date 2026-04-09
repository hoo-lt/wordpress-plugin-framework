<?php

namespace Hoo\WordPressPluginFramework\Repositories\Migrator;

class Repository implements RepositoryInterface
{
	protected const OPTION = 'hoo_wordpress_plugin_framework_database_migrator';

	protected array $hashes;

	public function __construct()
	{
		$this->hashes = get_option(self::OPTION, []);
	}

	public function has(string $hash): bool
	{
		return isset($this->hashes[$hash]);
	}

	public function add(string $hash): void
	{
		if ($this->has($hash)) {
			return;
		}

		$this->hashes[$hash] = time();

		update_option(self::OPTION, $this->hashes);
	}

	public function remove(string $hash): void
	{
		if (!$this->has($hash)) {
			return;
		}

		unset($this->hashes[$hash]);

		update_option(self::OPTION, $this->hashes);
	}
}
