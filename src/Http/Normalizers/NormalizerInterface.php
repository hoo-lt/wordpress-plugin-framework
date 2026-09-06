<?php

namespace Hoo\WordPressPluginFramework\Http\Normalizers;

interface NormalizerInterface
{
	public function normalize(mixed $unnormalized): mixed;
	public function normalizes(mixed $unnormalized): bool;
}
