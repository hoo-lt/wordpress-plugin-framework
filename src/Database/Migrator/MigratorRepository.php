<?php

namespace Hoo\WordPressPluginFramework\Database\Migrator;

class MigratorRepository implements MigratorRepositoryInterface
{
	private const OPTION = 'hoo_migrator_applied';

	protected array $applied = [];

	public function applied(): array
	{
		return $this->applied ??= get_option(self::OPTION, []);
	}

	public function apply(string $name): void
	{
		$this->applied();
		$this->applied[] = $name;

		update_option(self::OPTION, $this->applied);
	}
}
