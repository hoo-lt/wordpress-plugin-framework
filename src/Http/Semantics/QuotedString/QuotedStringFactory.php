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

	public function tryCreate(?string $quotedString): ?QuotedStringInterface
	{
		if ($quotedString === null) {
			return null;
		}

		return $this->create($quotedString);
	}

	protected function validate(string $quotedString): void
	{
		if (preg_match('/\A' . QuotedString::PATTERN . '\z/', $quotedString) !== 1) {
			throw new QuotedStringFactoryException('not a valid quoted string');
		}
	}

	/**
	 * unframes and, as RFC 9110 section 5.6.4 requires of recipients,
	 * replaces each quoted pair with the octet following the backslash
	 */
	protected function normalize(string $quotedString): string
	{
		return preg_replace('/\\\\(.)/s', '$1', substr($quotedString, 1, -1));
	}
}
