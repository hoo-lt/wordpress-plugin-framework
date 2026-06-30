<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer;

readonly class Normalizer implements NormalizerInterface
{
	public function normalize(array|object $body): array
	{
		return array_map(fn($value): mixed => is_array($value) || is_object($value) ? $this->normalize($value) : $value, is_array($body) ? $body : get_object_vars($body));
	}
}
