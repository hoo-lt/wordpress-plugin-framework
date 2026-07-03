<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer;

interface NormalizerInterface
{
	public function normalizes(mixed $unnormalized): bool;
	public function normalize(mixed $unnormalized): mixed;
}
