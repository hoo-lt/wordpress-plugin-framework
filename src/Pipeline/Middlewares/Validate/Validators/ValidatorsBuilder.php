<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators;

use Closure;
use Hoo\WordPressPluginFramework\{
    Pipeline\Middlewares\Validate\Validators\If\Validator,
    Pipeline\Middlewares\Validate\Validators\Comparison,
    Pipeline\Middlewares\Validate\Validators\Rules\ValidatorFactoryInterface,
    Pipeline\Middlewares\Validate\KeyValue\Body\KeyValue as Body,
    Pipeline\Middlewares\Validate\KeyValue\BodyQuery\KeyValue as BodyQuery,
    Pipeline\Middlewares\Validate\KeyValue\Query\KeyValue as Query,
    Pipeline\Middlewares\Validate\KeyValue\Header\KeyValue as Header,
    Pipeline\Middlewares\Validate\KeyValue\Route\KeyValue as Route,
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
                new Body($key),
                $closure,
            ),
        );
    }

    public function bodyQuery(string $key, Closure $closure): static
    {
        return $this->withValidator(
            $this->validatorFactory->create(
                new BodyQuery($key),
                $closure,
            ),
        );
    }

    public function query(string $key, Closure $closure): static
    {
        return $this->withValidator(
            $this->validatorFactory->create(
                new Query($key),
                $closure,
            ),
        );
    }

    public function header(string $key, Closure $closure): static
    {
        return $this->withValidator(
            $this->validatorFactory->create(
                new Header($key),
                $closure,
            ),
        );
    }

    public function route(string $key, Closure $closure): static
    {
        return $this->withValidator(
            $this->validatorFactory->create(
                new Route($key),
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

    public function compareBody(Closure $closure): static
    {
        return $this->buildComparison($closure, fn(string $key) => new Body($key));
    }

    public function compareBodyQuery(Closure $closure): static
    {
        return $this->buildComparison($closure, fn(string $key) => new BodyQuery($key));
    }

    public function compareQuery(Closure $closure): static
    {
        return $this->buildComparison($closure, fn(string $key) => new Query($key));
    }

    public function compareHeader(Closure $closure): static
    {
        return $this->buildComparison($closure, fn(string $key) => new Header($key));
    }

    public function compareRoute(Closure $closure): static
    {
        return $this->buildComparison($closure, fn(string $key) => new Route($key));
    }

    protected function buildComparison(Closure $closure, Closure $keyValue): static
    {
        $builder = $closure(new Comparison\Builder($keyValue));
        if (!$builder instanceof Comparison\BuilderInterface) {
            throw new ValidatorsBuilderException('not an instance of comparison builder');
        }

        return $this->withValidator($builder->build());
    }

    public function build(): array
    {
        return $this->validators;
    }
}
