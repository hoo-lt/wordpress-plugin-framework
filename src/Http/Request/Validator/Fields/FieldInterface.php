<?php

namespace Hoo\WordPressPluginFramework\Http\Request\Validator\Fields;

use Hoo\WordPressPluginFramework\Http\Method\Method;
use Hoo\WordPressPluginFramework\Http\Request\Validator\Rules\RuleInterface;

interface FieldInterface
{
	public function name(): string;
	public function method(): Method;
	public function rules(): array;
	public function withRule(RuleInterface $rule): FieldInterface;
}
