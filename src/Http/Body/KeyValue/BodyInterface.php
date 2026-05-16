<?php

namespace Hoo\WordPressPluginFramework\Http\Body\KeyValue;

use Hoo\WordPressPluginFramework\Http;

interface BodyInterface extends Http\KeyValue\KeyValueInterface
{
	public function __toString(): string;
}
