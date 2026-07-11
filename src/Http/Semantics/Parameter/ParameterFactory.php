<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Parameter;

use Hoo\WordPressPluginFramework\Http\Semantics\Semantics;

readonly class ParameterFactory implements ParameterFactoryInterface
{
	// parameter = parameter-name "=" parameter-value, RFC 9110 §5.6.6 — the wire is one bare
	// parameter, its OWS ";" OWS framing already consumed by the caller's scan
	protected const PARAMETER = '/\A' . Semantics::PARAMETER . '/';

	public function create(string $parameter): ParameterInterface
	{
		preg_match(self::PARAMETER, $parameter, $matched, PREG_UNMATCHED_AS_NULL);

		$quoted = $matched['quoted'] ?? null;   // null = the token branch fired (non-participating group), or the wire is no parameter at all

		return new Parameter(
			strtolower($matched['name'] ?? ''),                                       // parameter names are case-insensitive
			$quoted !== null ? $this->unquote($quoted) : ($matched['token'] ?? ''),   // token verbatim; quoted-string unquoted
		);
	}

	private function unquote(string $quotedString): string
	{
		return preg_replace('/\A' . Semantics::DQUOTE . '|\\\\(.)|' . Semantics::DQUOTE . '\z/s', '$1', $quotedString);   // strip the surrounding DQUOTEs, unescape quoted-pairs
	}
}
