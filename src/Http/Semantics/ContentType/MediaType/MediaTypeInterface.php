<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\ContentType\MediaType;

interface MediaTypeInterface
{
	public function type(): string;
	public function subtype(): string;

	public function parameters(): array;
	public function parameter(string $name): ?string;

	public function charset(): ?string;
}
