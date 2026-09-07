<?php

namespace Hoo\WordPressPluginFramework\Http\Abnf;

readonly class Rfc6838
{
	// RFC 6838 §4.2
	public const TYPE_NAME = self::RESTRICTED_NAME;
	public const SUBTYPE_NAME = self::RESTRICTED_NAME;
	public const RESTRICTED_NAME = self::RESTRICTED_NAME_FIRST . '(?:' . self::RESTRICTED_NAME_CHARS . '){0,126}';
	public const RESTRICTED_NAME_FIRST = '(?:' . Rfc5234::ALPHA . '|' . Rfc5234::DIGIT . ')';
	public const RESTRICTED_NAME_CHARS = '(?:' . Rfc5234::ALPHA . '|' . Rfc5234::DIGIT . '|!|#|\$|&|-|\^|_|\.|\+)';

	// RFC 6838 §4.3
	public const PARAMETER_NAME = self::RESTRICTED_NAME;
}
