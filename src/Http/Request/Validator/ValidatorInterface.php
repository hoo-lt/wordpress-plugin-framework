<?php

namespace Hoo\WordPressPluginFramework\Http\Request\Validator;

use Hoo\WordPressPluginFramework\Http\Request\Validator\Fields\FieldInterface;
use Hoo\WordPressPluginFramework\Http\Request\Validator\Rules\RuleInterface;

interface ValidatorInterface
{
	public function withField(FieldInterface $field): ValidatorInterface;
	public function withRule(RuleInterface $rule): ValidatorInterface;
	public function __invoke(): void;
}
