<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType;

use Hoo\WordPressPluginFramework\Http\Abnf\Rfc9110;

readonly class MediaType implements MediaTypeInterface
{
	protected string $type;
	protected string $subtype;

	public function __construct(
		string $type,
		string $subtype,
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

	public function __tostring(): string
	{
		return "{$this->type}/{$this->subtype}";
	}

	protected function validateType(string $type): void
	{
		if (!preg_match('@\A' . Rfc9110::TYPE . '\z@', $type)) {
			throw new MediaTypeException('invalid type');
		}

		if ($type === '*') {
			throw new MediaTypeException('type must not be a wildcard');
		}
	}

	protected function validateSubtype(string $subtype): void
	{
		if (!preg_match('@\A' . Rfc9110::SUBTYPE . '\z@', $subtype)) {
			throw new MediaTypeException('invalid subtype');
		}

		if ($subtype === '*') {
			throw new MediaTypeException('subtype must not be a wildcard');
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
}
