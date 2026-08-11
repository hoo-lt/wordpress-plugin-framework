<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Parameters;

use Hoo\WordPressPluginFramework\Http\Semantics\Semantics;

readonly class ParametersFactory implements ParametersFactoryInterface
{
	public function create(string $parameters): ParametersInterface
	{
		preg_match_all('/' . Semantics::PARAMETERS . '/', $parameters, $matches, PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL);

		$parameters = [];

		foreach ($matches as $match) {
			$parameters[$match['name']] = $match['quoted_string'] !== null ? $this->unquote($match['quoted_string']) : $match['token'];
		}

		return new Parameters($parameters);
	}

	protected function unquote(string $quotedString): string
	{
		return preg_replace('/\A' . Semantics::DQUOTE . '|\x5C(.)|' . Semantics::DQUOTE . '\z/s', '$1', $quotedString);
	}
}
