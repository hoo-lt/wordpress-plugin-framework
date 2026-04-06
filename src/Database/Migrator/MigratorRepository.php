<?php

namespace Hoo\WordPressPluginFramework\Database\Migrator;

class MigratorRepository implements MigratorRepositoryInterface
{
	protected array $applied;

	public function __construct(
		protected readonly string $option,
	) {
		$this->applied = get_option($this->option, []);
	}

	public function applied(): array
	{
		return $this->applied;
	}

	public function apply(string $name): void
	{
		$this->applied[] = $name;

		update_option($this->option, $this->applied);
	}
}
