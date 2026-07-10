<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Weight;

interface WeightFactoryInterface
{
	public function create(string $mediaRange): ?WeightInterface;
}
