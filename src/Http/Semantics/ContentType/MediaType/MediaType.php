<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\ContentType\MediaType;

use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\ParametersInterface;
use Traversable;

readonly class MediaType implements MediaTypeInterface
{
	protected string $type;
	protected string $subtype;

	public function __construct(
		string $type,
		string $subtype,
		protected ParametersInterface $parameters,
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

	public function parameters(): Traversable
	{
		return $this->parameters;
	}

	public function parameter(string $name): ?string
	{
		return $this->parameters->parameter($name);
	}

	public function charset(): ?string
	{
		$charset = $this->parameter('charset');
		return $charset === null ? null : strtolower($charset);
	}

	public function __toString(): string
	{
		return "{$this->type}/{$this->subtype}{$this->parameters}";
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
			throw new MediaTypeException('type is mandatory');
		}

		if ($type === '*') {
			throw new MediaTypeException('type must not be a wildcard');
		}
	}

	protected function validateSubtype(string $subtype): void
	{
		if ($subtype === '') {
			throw new MediaTypeException('subtype is mandatory');
		}

		if ($subtype === '*') {
			throw new MediaTypeException('subtype must not be a wildcard');
		}
	}
}
