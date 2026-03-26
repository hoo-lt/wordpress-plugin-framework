<?php

namespace Hoo\WordPressPluginFramework\Json;

interface JsonInterface
{
	public function decode(string $json): array;
	public function encode(array $json): string;
}