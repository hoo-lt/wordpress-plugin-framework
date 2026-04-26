<?php

namespace Hoo\WordPressPluginFramework\Json;

interface JsonInterface
{
	public function decode(string $json): mixed;
	public function encode(mixed $json): string;
}