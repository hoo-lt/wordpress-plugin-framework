<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer;

interface TypeNormalizerInterface
{
	public function supports(mixed $value): bool;
	public function normalize(mixed $value, NormalizerInterface $normalizer): mixed;
}
