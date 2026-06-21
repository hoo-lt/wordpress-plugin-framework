<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules;

use Closure;
use Hoo\WordPressPluginFramework\{
	Localization\Translator\TranslatorInterface,
	Pipeline\Middlewares\Validate\Validators\Rule\Rules\RuleInterface,
};

abstract readonly class AbstractRule implements RuleInterface
{
	public function __construct(
		protected TranslatorInterface $translator,
		protected ?string $message = null,
	) {
	}

	public function break(mixed $value, Closure $closure): bool
	{
		$break = $this->normalize($value) === null;
		if ($break) {
			$closure(
				$this->message ?? $this->message()
			);
		}

		return $break;
	}

	abstract protected function normalize(mixed $value): mixed;
	abstract protected function message(): string;
}
