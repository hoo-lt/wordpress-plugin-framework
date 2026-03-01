<?php

namespace Hoo\WordPressPluginFramework\Http\Url;

enum Scheme: string
{
	case Http = 'http';
	case Https = 'https';

	public function __toString(): string
	{
		return $this->value;
	}
}