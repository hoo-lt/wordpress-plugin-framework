<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Accept\MediaRange;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\Accept\MediaRange\Precedence\Precedence,
	Http\Semantics\ContentType\MediaType\MediaType,
	Http\Semantics\ContentType\MediaType\MediaTypeInterface,
};

readonly class MediaRange implements MediaRangeInterface
{
	public function __construct(
		protected string $type,
		protected string $subtype,
		protected array $parameters = [],
		protected float $q = 1.000,
	) {
	}

	public function type(): string
	{
		return $this->type;
	}

	public function subtype(): string
	{
		return $this->subtype;
	}

	public function parameters(): array
	{
		return $this->parameters;
	}

	public function parameter(string $name): ?string
	{
		return $this->parameters[strtolower($name)] ?? null;
	}

	public function charset(): ?string
	{
		$charset = $this->parameter('charset');
		return $charset === null ? null : strtolower($charset);
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

		return new MediaType($this->type, $this->subtype, $this->parameters);
	}

	public function precedence(MediaTypeInterface $mediaType): ?Precedence
	{
		if (
			$this->type === $mediaType->type() &&
			$this->subtype === $mediaType->subtype() &&
			$this->parameters === $mediaType->parameters()
		) {
			return Precedence::TypeSubtypeParameters;
		}

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
}
