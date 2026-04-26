<?php

namespace Hoo\WordPressPluginFramework\Http\Body\Json;

use Hoo\WordPressPluginFramework\{
	Helpers,
	Http,
};

readonly class Body implements Http\Body\BodyInterface
{
	public function __construct(
		protected Helpers\Array\HelperInterface $arrayHelper,
		protected Http\Coders\Json\CoderInterface $jsonCoder,
		protected mixed $json,
	) {
	}

	public function values(string $key = ''): array
	{
		if ($key === '') {
			return [
				'' => $this->json,
			];
		} else {
			if (!is_array($this->json)) {
				throw new Http\Body\BodyException('cant perform on scalar');
			}

			return $this->arrayHelper->values(
				$this->json,
				$key
			);
		}
	}

	public function value(string $key = ''): mixed
	{
		if ($key === '') {
			return $this->json;
		} else {
			if (!is_array($this->json)) {
				throw new Http\Body\BodyException('cant perform on scalar');
			}

			return $this->arrayHelper->value(
				$this->json,
				$key
			);
		}
	}

	public function withValue(string $key, mixed $value): static
	{
		if (!is_array($this->json)) {
			throw new Http\Body\BodyException('cant perform on scalar');
		}

		return new static(
			$this->arrayHelper,
			$this->jsonCoder,
			$this->arrayHelper->withValue(
				$this->json,
				$key,
				$value
			)
		);
	}

	public function withoutValue(string $key): static
	{
		if (!is_array($this->json)) {
			throw new Http\Body\BodyException('cant perform on scalar');
		}

		return new static(
			$this->arrayHelper,
			$this->jsonCoder,
			$this->arrayHelper->withoutValue(
				$this->json,
				$key
			)
		);
	}

	public function __toString(): string
	{
		return $this->jsonCoder->encode($this->json);
	}
}