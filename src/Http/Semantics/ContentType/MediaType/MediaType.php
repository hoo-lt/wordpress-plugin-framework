<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\ContentType\MediaType;

readonly class MediaType implements MediaTypeInterface
{
	public function __construct(
		protected string $type,
		protected string $subtype,
		protected array $parameters = [],
	) {
		$this->validateType($type);
		$this->validateSubtype($subtype);
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
