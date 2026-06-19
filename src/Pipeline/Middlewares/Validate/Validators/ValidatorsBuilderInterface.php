<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators;

use Closure;

interface ValidatorsBuilderInterface
{
    public function validators(): array;
    public function withValidators(ValidatorInterface ...$validators): static;
    public function withoutValidators(): static;

    public function withValidator(ValidatorInterface $validator): static;

    public function body(string $key, Closure $closure): static;
    public function bodyQuery(string $key, Closure $closure): static;
    public function query(string $key, Closure $closure): static;
    public function header(string $key, Closure $closure): static;
    public function route(string $key, Closure $closure): static;

    public function if(Closure $expressionValidatorsClosure, Closure $statementValidatorsClosure): static;

    public function build(): array;
}
