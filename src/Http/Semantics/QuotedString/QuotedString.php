<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\QuotedString;

readonly class QuotedString implements QuotedStringInterface
{
	/**
	 * quoted-string = DQUOTE *( qdtext / quoted-pair ) DQUOTE per RFC 9110 section 5.6.4
	 *
	 * qdtext = HTAB / SP / %x21 / %x23-5B / %x5D-7E / obs-text
	 * quoted-pair = "\" ( HTAB / SP / VCHAR / obs-text )
	 */
	public const PATTERN = '"(?:[\x09\x20\x21\x23-\x5B\x5D-\x7E\x80-\xFF]|\\\\[\x09\x20-\x7E\x80-\xFF])*+"';

	protected string $quotedString;

	public function __construct(
		string $quotedString,
	) {
		$this->validate($quotedString);
		$this->quotedString = $this->normalize($quotedString);
	}

	public function value(): string
	{
		return $this->quotedString;
	}

	public function __toString(): string
	{
		return '"' . preg_replace('/([\\\\"])/', '\\\\$1', $this->quotedString) . '"';
	}

	protected function validate(string $quotedString): void
	{
		if (preg_match('/\A' . static::PATTERN . '\z/', $quotedString) !== 1) {
			throw new QuotedStringException('not a valid quoted string');
		}
	}

	protected function normalize(string $quotedString): string
	{
		return preg_replace('/\\\\(.)/s', '$1', substr($quotedString, 1, -1));
	}
}
