<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Parameter;

use Hoo\WordPressPluginFramework\Http\Semantics\Semantics;

readonly class ParameterFactory implements ParameterFactoryInterface
{
	public function create(string $parameter): ParameterInterface
	{
		preg_match('/\A' . Semantics::PARAMETER . '/', $parameter, $matched, PREG_UNMATCHED_AS_NULL);

		$quotedString = $matched['quoted_string'] ?? null;

		return new Parameter(
			strtolower($matched['name'] ?? ''),
			$quotedString !== null ? $this->unquote($quotedString) : ($matched['token'] ?? ''),
		);
	}

	private function unquote(string $quotedString): string
	{
		return preg_replace('/\A' . Semantics::DQUOTE . '|\x5C(.)|' . Semantics::DQUOTE . '\z/s', '$1', $quotedString);
	}
}
