<?php

namespace Hoo\WordPressPluginFramework\Hooker;

readonly class Hooker implements HookerInterface
{
	public function __construct(
		protected array $hooks,
	) {
	}

	public function hooks(): array
	{
		return $this->hooks;
	}

	public function __invoke(): void
	{
		foreach ($this->hooks as $hook) {
			$hook();
		}
	}
}
