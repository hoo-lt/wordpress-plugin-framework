<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer;

interface NormalizerInterface
{
	public function supports(mixed $value): bool;
	public function normalize(mixed $value): mixed;
}
