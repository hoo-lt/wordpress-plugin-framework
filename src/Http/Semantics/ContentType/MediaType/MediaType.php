<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\ContentType\MediaType;

readonly class MediaType implements MediaTypeInterface
{
	public function __construct(
		protected string $type,
		protected string $subtype,
		protected array $parameters = [],
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
}
