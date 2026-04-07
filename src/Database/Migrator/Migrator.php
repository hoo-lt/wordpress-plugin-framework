<?php

namespace Hoo\WordPressPluginFramework\Database\Migrator;

use wpdb;

readonly class Migrator implements MigratorInterface
{
	public function __construct(
		protected MigratorRepositoryInterface $repository,
		protected wpdb $wpdb,
		protected string $path,
	) {
	}

	public function migrate(): void
	{
		$applied = $this->repository->applied();

		foreach (glob($this->path . '/*.sql') as $file) {
			$sql = file_get_contents($file);
			$hash = md5($sql);

			if (in_array($hash, $applied, true)) {
				continue;
			}

			$sql = str_replace('{prefix}', $this->wpdb->prefix, $sql);

			$this->wpdb->query($sql);
			$this->repository->apply($hash);
		}
	}
}
