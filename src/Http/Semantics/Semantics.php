<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics;

readonly class Semantics
{
	// RFC 5234 §B.1 — core rules (the terminal alphabet, imported by RFC 9110 §5.6)
	public const HTAB = '\x09';
	public const SP = '\x20';
	public const DQUOTE = '\x22';
	public const VCHAR = '\x21-\x7E';
	public const WSP = self::SP . self::HTAB;

	// RFC 9110 §5.6.3 — OWS = *( SP / HTAB )
	public const OWS = '[' . self::WSP . ']*';

	// RFC 9110 §5.6.2 — token = 1*tchar
	public const TCHAR = '[!#$%&\'*+\-.^_`|~0-9A-Za-z]';
	public const TOKEN = self::TCHAR . '++';

	// RFC 9110 §5.6.4 — quoted-string = DQUOTE *( qdtext / quoted-pair ) DQUOTE
	//   qdtext      = HTAB / SP / %x21 / %x23-5B / %x5D-7E / obs-text
	//   quoted-pair = "\" ( HTAB / SP / VCHAR / obs-text )
	public const OBS_TEXT = '\x80-\xFF';
	public const QDTEXT = '[' . self::HTAB . self::SP . '\x21\x23-\x5B\x5D-\x7E' . self::OBS_TEXT . ']';
	public const QUOTED_PAIR = '\x5C[' . self::HTAB . self::SP . self::VCHAR . self::OBS_TEXT . ']';
	public const QUOTED_STRING = self::DQUOTE . '(?:' . self::QDTEXT . '|' . self::QUOTED_PAIR . ')*+' . self::DQUOTE;

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
	// its framing consumed, the parameter captured bare — the "*" is the caller's scan (preg_match_all)
	public const PARAMETERS = self::OWS . ';' . self::OWS . '(?<parameter>' . self::PARAMETER . ')';

	// RFC 9110 §12.4.2 — weight = OWS ";" OWS ( "q" / "Q" ) "=" qvalue
	public const WEIGHT = self::OWS . ';' . self::OWS . '[qQ]=(?<q>' . self::QVALUE . ')';
}
