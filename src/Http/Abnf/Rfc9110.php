<?php

namespace Hoo\WordPressPluginFramework\Http\Abnf;

readonly class Rfc9110
{
	// RFC 9110 §5.6.3 — OWS = *( SP / HTAB )
	public const OWS = '[' . Rfc5234::WSP . ']*';

	// RFC 9110 §5.6.2 — token = 1*tchar
	public const TCHAR = '[!#$%&\'*+\-.^_`|~0-9A-Za-z]';
	public const TOKEN = self::TCHAR . '++';

	// RFC 9110 §5.6.4 — quoted-string = DQUOTE *( qdtext / quoted-pair ) DQUOTE
	//   qdtext      = HTAB / SP / %x21 / %x23-5B / %x5D-7E / obs-text
	//   quoted-pair = "\" ( HTAB / SP / VCHAR / obs-text )
	public const OBS_TEXT = '\x80-\xFF';
	public const QDTEXT = '[' . Rfc5234::HTAB . Rfc5234::SP . '\x21\x23-\x5B\x5D-\x7E' . self::OBS_TEXT . ']';
	public const QUOTED_PAIR = '\x5C[' . Rfc5234::HTAB . Rfc5234::SP . Rfc5234::VCHAR . self::OBS_TEXT . ']';
	public const QUOTED_STRING = Rfc5234::DQUOTE . '(?:' . self::QDTEXT . '|' . self::QUOTED_PAIR . ')*+' . Rfc5234::DQUOTE;

	// RFC 9110 §5.1 — field-name = token
	public const FIELD_NAME = self::TOKEN;

	// RFC 9110 §5.5 — field-vchar = VCHAR / obs-text
	public const FIELD_VCHAR = '[' . Rfc5234::VCHAR . self::OBS_TEXT . ']';

	// RFC 9110 §5.5 — field-content = field-vchar [ 1*( SP / HTAB / field-vchar ) field-vchar ]
	public const FIELD_CONTENT = self::FIELD_VCHAR . '(?:[' . Rfc5234::WSP . ']*' . self::FIELD_VCHAR . ')*';

	// RFC 9110 §5.5 — field-value = *field-content
	public const FIELD_VALUE = '(?:' . self::FIELD_CONTENT . ')?';

	// RFC 9110 §12.4.2 — qvalue = ( "0" [ "." 0*3DIGIT ] ) / ( "1" [ "." 0*3("0") ] )
	public const QVALUE = '(?:0(?:\.[0-9]{0,3})?|1(?:\.0{0,3})?)';

	// RFC 9110 §8.3.1 — type = token
	public const TYPE = '(?<type>' . self::TOKEN . ')';

	// RFC 9110 §8.3.1 — subtype = token
	public const SUBTYPE = '(?<subtype>' . self::TOKEN . ')';

	// RFC 9110 §5.6.6 — parameter-name = token
	public const PARAMETER_NAME = '(?<name>' . self::TOKEN . ')';

	// RFC 9110 §5.6.6 — parameter-value = ( token / quoted-string ); named branches so the match tells which alternative fired
	public const PARAMETER_VALUE = '(?:(?<quoted_string>' . self::QUOTED_STRING . ')|(?<token>' . self::TOKEN . '))';

	// RFC 9110 §5.6.6 — parameter = parameter-name "=" parameter-value
	public const PARAMETER = self::PARAMETER_NAME . '=' . self::PARAMETER_VALUE;

	// RFC 9110 §5.6.6 — parameters = *( OWS ";" OWS [ parameter ] ); one step of the repetition,
	// captured with its framing so the caller can hand the run on verbatim — the "*" is the caller's scan (preg_match_all)
	public const PARAMETERS = '(?<parameters>' . self::OWS . ';' . self::OWS . self::PARAMETER . ')';

	// RFC 9110 §12.4.2 — weight = OWS ";" OWS ( "q" / "Q" ) "=" qvalue
	public const WEIGHT = self::OWS . ';' . self::OWS . '[qQ]=(?<q>' . self::QVALUE . ')';
}
