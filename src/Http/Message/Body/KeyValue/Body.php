<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body\KeyValue;

use Hoo\WordPressPluginFramework\{
	Helpers\KeyValue\HelperInterface,
	Http\Message\Body\BodyInterface,
	Http\Coders\CoderInterface,
	Http\KeyValue\KeyValueInterface,
};

readonly class Body implements BodyInterface, KeyValueInterface
{
	public function __construct(
		protected HelperInterface $helper,
		protected CoderInterface $coder,
		protected object|array $body,
	) {
	}

	public function values(string $key): object|array
	{
		return $this->helper->values($this->body, $key);
	}

	public function value(string $key): mixed
	{
		return $this->helper->value($this->body, $key);
	}

	public function withValue(string $key, mixed $value): static
	{
		return new static(
			$this->helper,
			$this->coder,
			$this->helper->withValue($this->body, $key, $value),
		);
	}

	public function withoutValue(string $key): static
	{
		return new static(
			$this->helper,
			$this->coder,
			$this->helper->withoutValue($this->body, $key),
		);
	}

	public function __toString(): string
	{
		return $this->coder->encode($this->body);
	}
}