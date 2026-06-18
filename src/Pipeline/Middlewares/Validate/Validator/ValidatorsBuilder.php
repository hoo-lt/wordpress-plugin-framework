<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validator;

use Closure;
use Hoo\WordPressPluginFramework\{
    Pipeline\Middlewares\Validate\Validator\ValidatorInterface,
    Pipeline\Middlewares\Validate\Validator\If\Validator,
    Pipeline\Middlewares\Validate\Validator\Rules\ValidatorFactoryInterface,
    Pipeline\Middlewares\Validate\KeyValues\Body\KeyValues as BodyKeyValues,
    Pipeline\Middlewares\Validate\KeyValues\BodyQuery\KeyValues as BodyQueryKeyValues,
    Pipeline\Middlewares\Validate\KeyValues\Query\KeyValues as QueryKeyValues,
    Pipeline\Middlewares\Validate\KeyValues\Header\KeyValues as HeaderKeyValues,
    Pipeline\Middlewares\Validate\KeyValues\Route\KeyValues as RouteKeyValues,
};

readonly class ValidatorsBuilder implements ValidatorsBuilderInterface
{
    public function __construct(
        protected ValidatorFactoryInterface $validatorFactory,
        protected array $validators = [],
    ) {
    }

    public function validators(): array
    {
        return $this->validators;
    }

    public function withValidators(ValidatorInterface ...$validators): static
    {
        return new static($this->validatorFactory, $validators);
    }

    public function withoutValidators(): static
    {
        return new static($this->validatorFactory, []);
    }

    public function withValidator(ValidatorInterface $validator): static
    {
        return $this->withValidators(...$this->validators, $validator);
    }

    public function body(string $key, Closure $closure): static
    {
        return $this->withValidator(
            $this->validatorFactory->create(
                new BodyKeyValues($key),
                $closure,
            ),
        );
    }

    public function bodyQuery(string $key, Closure $closure): static
    {
        return $this->withValidator(
            $this->validatorFactory->create(
                new BodyQueryKeyValues($key),
                $closure,
            ),
        );
    }

    public function query(string $key, Closure $closure): static
    {
        return $this->withValidator(
            $this->validatorFactory->create(
                new QueryKeyValues($key),
                $closure,
            ),
        );
    }

    public function header(string $key, Closure $closure): static
    {
        return $this->withValidator(
            $this->validatorFactory->create(
                new HeaderKeyValues($key),
                $closure,
            ),
        );
    }

    public function route(string $key, Closure $closure): static
    {
        return $this->withValidator(
            $this->validatorFactory->create(
                new RouteKeyValues($key),
                $closure,
            ),
        );
    }

    public function if(Closure $expressionValidatorsClosure, Closure $statementValidatorsClosure): static
    {
        $expressionValidatorsBuilder = $expressionValidatorsClosure(
            new static($this->validatorFactory),
        );
        if (!$expressionValidatorsBuilder instanceof static) {
            throw new ValidatorsBuilderException('not an instance of validators builder');
        }

        $statementValidatorsBuilder = $statementValidatorsClosure(
            new static($this->validatorFactory),
        );
        if (!$statementValidatorsBuilder instanceof static) {
            throw new ValidatorsBuilderException('not an instance of validators builder');
        }

        return $this->withValidator(
            new Validator(
                $expressionValidatorsBuilder->build(),
                $statementValidatorsBuilder->build(),
            ),
        );
    }

    public function build(): array
    {
        return $this->validators;
    }
}
