<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer;

interface NormalizerInterface
{
	public function normalizes(mixed $value): bool;
	public function normalize(mixed $value): mixed;
}
