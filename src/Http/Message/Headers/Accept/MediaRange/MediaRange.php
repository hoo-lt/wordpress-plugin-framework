<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers\Accept\MediaRange;

use Hoo\WordPressPluginFramework\{
	Http\Message\Headers\Accept\MediaRange\Precedence\Precedence,
	Http\Message\Headers\ContentType\MediaType\MediaType,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
	Http\Abnf\Rfc9110,
};

readonly class MediaRange implements MediaRangeInterface
{
	protected string $type;
	protected string $subtype;
	protected float $q;

	public function __construct(
		string $type,
		string $subtype,
		string $q = '1',
	) {
		$this->validateType($type);
		$this->type = $this->normalizeType($type);

		$this->validateSubtype($subtype);
		$this->subtype = $this->normalizeSubtype($subtype);

		$this->validateQ($q);
		$this->q = $this->normalizeQ($q);
	}

	public function type(): string
	{
		return $this->type;
	}

	public function subtype(): string
	{
		return $this->subtype;
	}

	public function q(): float
	{
		return $this->q;
	}

	public function mediaType(): ?MediaTypeInterface
	{
		if (
			$this->type === '*' ||
			$this->subtype === '*'
		) {
			return null;
		}

		return new MediaType($this->type, $this->subtype);
	}

	public function precedence(MediaTypeInterface $mediaType): ?Precedence
	{
		if (
			$this->type === $mediaType->type() &&
			$this->subtype === $mediaType->subtype()
		) {
			return Precedence::TypeSubtype;
		}

		if (
			$this->type === $mediaType->type() &&
			$this->subtype === '*'
		) {
			return Precedence::TypeWildcardSubtype;
		}

		if (
			$this->type === '*'
		) {
			return Precedence::WildcardTypeWildcardSubtype;
		}

		return null;
	}

	protected function validateType(string $type): void
	{
		if (!preg_match('@\A' . Rfc9110::TYPE . '\z@', $type)) {
			throw new MediaRangeException('invalid type');
		}
	}

	protected function validateSubtype(string $subtype): void
	{
		if (!preg_match('@\A' . Rfc9110::SUBTYPE . '\z@', $subtype)) {
			throw new MediaRangeException('invalid subtype');
		}
	}

	protected function validateQ(string $q): void
	{
		if (!preg_match('@\A' . Rfc9110::QVALUE . '\z@', $q)) {
			throw new MediaRangeException('invalid q');
		}
	}

	protected function normalizeType(string $type): string
	{
		return strtolower($type);
	}

	protected function normalizeSubtype(string $subtype): string
	{
		return strtolower($subtype);
	}

	protected function normalizeQ(string $q): float
	{
		return (float) $q;
	}
}
