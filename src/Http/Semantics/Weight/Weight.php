<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Weight;

readonly class Weight implements WeightInterface
{
	public function __construct(
		protected float $weight,
	) {
	}

	public function value(): float
	{
		return $this->weight;
	}
}
