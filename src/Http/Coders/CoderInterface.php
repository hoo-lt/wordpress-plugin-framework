<?php

namespace Hoo\WordPressPluginFramework\Http\Coders;

interface CoderInterface
{
	public function decode(string $string): mixed;
	public function encode(mixed $mixed): string;
}