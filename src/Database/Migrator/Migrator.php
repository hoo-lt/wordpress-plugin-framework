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
		$paths = glob("{$this->path}/*.sql");
		if ($paths === false) {
			throw new MigratorException('unable to list migrations');
		}

		$queries = array_map(fn(string $path) => strtr(file_get_contents($path), [
			':prefix' => $this->wpdb->prefix,
		]), $paths);

		return array_combine(array_map(md5(...), $queries), $queries);
	}
}
