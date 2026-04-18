<?php

namespace Hoo\WordPressPluginFramework\Http\Request\Validator;

use Hoo\WordPressPluginFramework\Http\Method\Method;
use Hoo\WordPressPluginFramework\Http\Request\RequestInterface;
use Hoo\WordPressPluginFramework\Http\Request\Validator\Fields\FieldInterface;
use Hoo\WordPressPluginFramework\Http\Request\Validator\Rules\RuleException;
use Hoo\WordPressPluginFramework\Http\Request\Validator\Rules\RuleInterface;

readonly class Validator implements ValidatorInterface
{
	public function __construct(
		protected RequestInterface $request,
		protected array $fields = [],
		protected ?Fields\FieldInterface $currentField = null,
	) {
	}

	public function withField(FieldInterface $field): ValidatorInterface
	{
		return new self(
			$this->request,
			$this->resolvedFields(),
			$field,
		);
	}

	public function withRule(RuleInterface $rule): ValidatorInterface
	{
		return new self(
			$this->request,
			$this->fields,
			$this->currentField->withRule($rule),
		);
	}

	protected function resolvedFields(): array
	{
		return $this->currentField
			? [...$this->fields, $this->currentField]
			: $this->fields;
	}

	public function __invoke(): void
	{
		$errors = [];

		foreach ($this->resolvedFields() as $field) {
			$value = match ($field->method()) {
				Method::Post => $this->request->post($field->name()),
				Method::Get => $this->request->get($field->name()),
			};

			$fieldErrors = [];

			foreach ($field->rules() as $rule) {
				try {
					$rule($value);
				} catch (RuleException $ruleException) {
					$fieldErrors[] = "{$field->name()} {$ruleException->getMessage()}";
				}
			}

			if (!empty($fieldErrors)) {
				$errors[$field->name()] = $fieldErrors;
			}
		}

		if (!empty($errors)) {
			throw new ValidationException($errors);
		}
	}
}
