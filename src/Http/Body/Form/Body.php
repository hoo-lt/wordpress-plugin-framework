<?php

namespace Hoo\WordPressPluginFramework\Http\Body\Form;

use Hoo\WordPressPluginFramework\{
	Helpers,
	Http,
};

readonly class Body implements Http\Body\BodyInterface
{
	public function __construct(
		protected Helpers\Array\HelperInterface $arrayHelper,
		protected Http\Coders\Form\CoderInterface $formCoder,
		protected array $form,
	) {
	}

	public function values(string $key = ''): array
	{
		if ($key === '') {
			return [
				'' => $this->form,
			];
		}

		return $this->arrayHelper->values(
			$this->form,
			$key,
		);
	}

	public function value(string $key = ''): mixed
	{
		if ($key === '') {
			return $this->form;
		}

		return $this->arrayHelper->value(
			$this->form,
			$key,
		);
	}

	public function withValue(string $key, mixed $value): static
	{
		return new static(
			$this->arrayHelper,
			$this->formCoder,
			$this->arrayHelper->withValue(
				$this->form,
				$key,
				$value,
			),
		);
	}

	public function withoutValue(string $key): static
	{
		return new static(
			$this->arrayHelper,
			$this->formCoder,
			$this->arrayHelper->withoutValue(
				$this->form,
				$key,
			),
		);
	}

	public function __toString(): string
	{
		return $this->formCoder->encode($this->form);
	}
}