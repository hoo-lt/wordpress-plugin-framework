<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\QuotedString;

readonly class QuotedStringFactory implements QuotedStringFactoryInterface
{
	public function create(string $quotedString): QuotedStringInterface
	{
		$this->validate($quotedString);

		return new QuotedString(
			$this->normalize($quotedString),
		);
	}

	protected function validate(string $quotedString): void
	{
		if (preg_match('/\A"(?:[\x09\x20\x21\x23-\x5B\x5D-\x7E\x80-\xFF]|\\\\[\x09\x20-\x7E\x80-\xFF])*+"\z/', $quotedString) !== 1) {
			throw new QuotedStringFactoryException('not a valid quoted string');
		}
	}

	protected function normalize(string $quotedString): string
	{
		return preg_replace('/\A"|"\z|\\\\(.)/s', '$1', $quotedString);
	}
}
