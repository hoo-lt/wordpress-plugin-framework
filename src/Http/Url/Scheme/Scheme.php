<?php

namespace Hoo\WordPressPluginFramework\Http\Url\Scheme;

enum Scheme: string
{
	case Http = 'http';
	case Https = 'https';

	public static function create(string $method): static
	{
		$method = static::tryFrom($method);
		if ($method === null) {
			throw new SchemeException("invalid scheme");
		}

		return $method;
	}

	public function port(): int
	{
		return match ($this) {
			self::Http => 80,
			self::Https => 443,
		};
	}
}
