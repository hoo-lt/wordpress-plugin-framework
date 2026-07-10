<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Parameters;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\Parameters\Parameter\Parameter,
	Http\Semantics\Semantics,
};

readonly class ParametersFactory implements ParametersFactoryInterface
{
	// parameters = *( OWS ";" OWS [ parameter ] ), RFC 9110 §5.6.6 — the OWS after ";" is the grammar's own, not list framing; the unanchored scan skips any OWS before ";"
	protected const PARAMETER = '/;' . Semantics::OWS . Semantics::PARAMETER . '/';

	public function create(string $parameters): ParametersInterface
	{
		preg_match_all(self::PARAMETER, $parameters, $matches, PREG_SET_ORDER);

		$parameters = [];

		foreach ($matches as $parameter) {
			$quoted = $parameter['quoted'] ?? '';   // '' when the token branch fired (unmatched group); a quoted-string is always ≥2 chars ("")

			$parameters[] = new Parameter(
				strtolower($parameter['name']),                                  // parameter names are case-insensitive
				$quoted !== '' ? $this->unquote($quoted) : $parameter['token'],   // token verbatim; quoted-string unquoted
			);
		}

		return new Parameters($parameters);
	}

	private function unquote(string $quotedString): string
	{
		return preg_replace('/\A' . Semantics::DQUOTE . '|\\\\(.)|' . Semantics::DQUOTE . '\z/s', '$1', $quotedString);   // strip the surrounding DQUOTEs, unescape quoted-pairs
	}
}
