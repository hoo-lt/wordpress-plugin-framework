<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer;

interface NormalizerInterface
{
	public function normalize(mixed $value): mixed;
}
