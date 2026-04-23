<?php

namespace Hoo\WordPressPluginFramework\Http\Url\Scheme;

enum Scheme: string
{
	case Http = 'http';
	case Https = 'https';

	public function port(): int
	{
		return match($this) {
			self::Http  => 80,
			self::Https => 443,
		};
	}
}
