<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers\Accept\MediaRange;

use Hoo\WordPressPluginFramework\{
	Http\Message\Headers\Accept\MediaRange\Precedence\Precedence,
	Http\Message\Headers\ContentType\MediaType\MediaType,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
};

readonly class MediaRange implements MediaRangeInterface
{
	protected string $type;
	protected string $subtype;

	public function __construct(
		string $type,
		string $subtype,
		protected float $q = 1.000,
	) {
		$this->validateType($type);
		$this->type = $this->normalizeType($type);

		$this->validateSubtype($subtype);
		$this->subtype = $this->normalizeSubtype($subtype);
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

	public function __toString(): string
	{
		return "{$this->type}/{$this->subtype}{$this->weight()}";
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
			return Precedence::WildcardType;
		}

		return null;
	}

	protected function weight(): string
	{
		return ';q=' . number_format($this->q, 3, '.', '');
	}

	protected function normalizeType(string $type): string
	{
		return strtolower($type);
	}

	protected function normalizeSubtype(string $subtype): string
	{
		return strtolower($subtype);
	}

	protected function validateType(string $type): void
	{
		if ($type === '') {
			throw new MediaRangeException('type is mandatory');
		}
	}

	protected function validateSubtype(string $subtype): void
	{
		if ($subtype === '') {
			throw new MediaRangeException('subtype is mandatory');
		}
	}
}
