<?php

namespace Hoo\WordPressPluginFramework\Database\Migrator;

use Hoo\WordPressPluginFramework\Repositories\Migrator\RepositoryInterface;
use wpdb;

readonly class Migrator implements MigratorInterface
{
	public function __construct(
		protected RepositoryInterface $repository,
		protected wpdb $wpdb,
		protected string $path,
	) {
	}

	public function migrate(): void
	{
		$queries = $this->queries();

		foreach ($queries as $hash => $query) {
			if ($this->repository->has($hash)) {
				continue;
			}

			$this->wpdb->query($query);

			if ($this->wpdb->last_error) {
				throw new MigratorException($this->wpdb->last_error);
			}

			$this->repository->add($hash);
		}
	}

	protected function queries(): array
	{
		$queries = [];

		foreach (glob("{$this->path}/*.sql") as $path) {
			$query = file_get_contents($path);
			$hash = md5($query);

			$queries[$hash] = strtr($query, [
				':prefix' => $this->wpdb->prefix,
			]);
		}

		return $queries;
	}
}
