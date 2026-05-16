<?php

namespace Hoo\WordPressPluginFramework\Http\Url\Query;

use Hoo\WordPressPluginFramework\Http;

interface QueryInterface extends Http\KeyValue\KeyValueInterface
{
	public function __toString(): string;
}
