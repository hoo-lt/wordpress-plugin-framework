<?php

namespace Hoo\WordPressPluginFramework\Hooker;

use Closure;
use Hoo\WordPressPluginFramework\Hooker\Hooks\HooksBuilderInterface;

readonly class HookerFactory implements HookerFactoryInterface
{
	public function __construct(
		protected HooksBuilderInterface $hooksBuilder,
	) {
	}

	public function create(Closure $closure): HookerInterface
	{
		return new Hooker(
			$this->buildHooks($closure),
		);
	}

	protected function buildHooks(Closure $closure): array
	{
		$hooksBuilder = $closure($this->hooksBuilder);
		if (!$hooksBuilder instanceof HooksBuilderInterface) {
			throw new HookerFactoryException('closure must return hooks builder instance');
		}

		return $hooksBuilder->build();
	}
}
