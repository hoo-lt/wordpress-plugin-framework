<?php

namespace Hoo\WordPressPluginFramework\Database\Migrator;

readonly class Migrator implements MigratorInterface
{
	public function __construct(
		protected MigratorRepositoryInterface $repository,
		protected array $migrations,
	) {
	}

	public function migrate(): void
	{
		$applied = $this->repository->applied();

		foreach ($this->migrations as $migration) {
			if (in_array($migration::class, $applied, true)) {
				continue;
			}

			$migration->up();
			$this->repository->apply($migration::class);
		}
	}
}
