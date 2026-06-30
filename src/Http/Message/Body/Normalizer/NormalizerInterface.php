<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer;

interface NormalizerInterface
{
	public function normalize(array|object $body): array;
}
