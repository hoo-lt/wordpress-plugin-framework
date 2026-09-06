<?php

namespace Hoo\WordPressPluginFramework\Http\Normalizers;

interface NormalizersInterface
{
	public function get(mixed $unnormalized): ?NormalizerInterface;
}
