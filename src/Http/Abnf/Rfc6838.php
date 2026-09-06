<?php

namespace Hoo\WordPressPluginFramework\Http\Abnf;

readonly class Rfc6838
{
	// RFC 6838 §4.2 — restricted-name-first = ALPHA / DIGIT
	public const RESTRICTED_NAME_FIRST = Rfc5234::ALPHA . Rfc5234::DIGIT;

	// RFC 6838 §4.2 — restricted-name-chars = ALPHA / DIGIT / "!" / "#" / "$" / "&" / "-" / "^" / "_" / "." / "+"
	public const RESTRICTED_NAME_CHARS = self::RESTRICTED_NAME_FIRST . '!#$&\-^_.+';

	// RFC 6838 §4.2 — restricted-name = restricted-name-first *126restricted-name-chars
	public const RESTRICTED_NAME = '[' . self::RESTRICTED_NAME_FIRST . '][' . self::RESTRICTED_NAME_CHARS . ']{0,126}';
}
