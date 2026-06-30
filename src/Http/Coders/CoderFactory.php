<?php

namespace Hoo\WordPressPluginFramework\Http\Coders;

readonly class CoderFactory implements CoderFactoryInterface
{
	public function __construct(
		protected array $coders,
	) {
	}

	public function create(string $mediaType): CoderInterface
	{
		$coder = $this->tryCreate($mediaType);
		if ($coder === null) {
			throw new CoderFactoryException('failed to create coder');
		}

		return $coder;
	}

	public function tryCreate(?string $mediaType): ?CoderInterface
	{
		$coder = $this->coders[$mediaType] ?? null;
		if ($coder === null) {
			return null;
		}

		return $coder;
	}
}