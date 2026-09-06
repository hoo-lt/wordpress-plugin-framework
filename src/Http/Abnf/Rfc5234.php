<?php

namespace Hoo\WordPressPluginFramework\Http\Abnf;

readonly class Rfc5234
{
	// RFC 5234 §B.1 — core rules (the terminal alphabet, imported by RFC 9110 §5.6)
	public const HTAB = '\x09';
	public const SP = '\x20';
	public const DQUOTE = '\x22';
	public const VCHAR = '\x21-\x7E';
	public const WSP = self::SP . self::HTAB;
	public const ALPHA = '\x41-\x5A\x61-\x7A';
	public const DIGIT = '\x30-\x39';
}
