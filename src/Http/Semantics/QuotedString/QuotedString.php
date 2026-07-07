<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\QuotedString;

readonly class QuotedString implements QuotedStringInterface
{
	public function __construct(
		protected string $quotedString,
	) {
		$this->validate($quotedString);
	}

	public function __toString(): string
	{
		return $this->quotedString;
	}

	protected function validate(string $quotedString): void
	{
		if (preg_match('/\A[\x09\x20-\x7E\x80-\xFF]*+\z/', $quotedString) !== 1) {
			throw new QuotedStringException('not a valid quoted string value');
		}
	}
}
