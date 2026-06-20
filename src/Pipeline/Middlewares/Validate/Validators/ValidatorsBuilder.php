<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators;

use Closure;
use Hoo\WordPressPluginFramework\{
    Pipeline\Middlewares\Validate\Validators\Condition\Validator as ConditionValidator,
    Pipeline\Middlewares\Validate\Validators\Rule\ValidatorFactoryInterface as RuleValidatorFactoryInterface,
    Pipeline\Middlewares\Validate\KeyValue\Body\KeyValue as Body,
    Pipeline\Middlewares\Validate\KeyValue\BodyQuery\KeyValue as BodyQuery,
    Pipeline\Middlewares\Validate\KeyValue\Query\KeyValue as Query,
    Pipeline\Middlewares\Validate\KeyValue\Header\KeyValue as Header,
    Pipeline\Middlewares\Validate\KeyValue\Route\KeyValue as Route,
    Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\DateTime\ComparatorFactoryInterface as DateTimeComparatorFactoryInterface,
    Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\Float\Comparator as FloatComparator,
    Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\Int\Comparator as IntComparator,
    Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\String\Comparator as StringComparator,
    Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\ComparatorInterface,
    Pipeline\Middlewares\Validate\Validators\Comparison\ValidatorBuilderInterface as ComparisonValidatorBuilderInterface,
};

readonly class ValidatorsBuilder implements ValidatorsBuilderInterface
{
    public function __construct(
        protected RuleValidatorFactoryInterface $ruleValidatorFactory,
        protected DateTimeComparatorFactoryInterface $dateTimeComparatorFactory,
        protected ComparisonValidatorBuilderInterface $comparisonValidatorBuilder,
        protected array $validators = [],
    ) {
    }

    public function validators(): array
    {
        return $this->validators;
    }

    public function withValidators(ValidatorInterface ...$validators): static
    {
        return new static($this->ruleValidatorFactory, $this->dateTimeComparatorFactory, $this->comparisonValidatorBuilder, $validators);
    }

    public function withoutValidators(): static
    {
        return new static($this->ruleValidatorFactory, $this->dateTimeComparatorFactory, $this->comparisonValidatorBuilder, []);
    }

    public function withValidator(ValidatorInterface $validator): static
    {
        return $this->withValidators(...$this->validators, $validator);
    }

    public function body(string $key, Closure $closure): static
    {
        return $this->withValidator(
            $this->ruleValidatorFactory->create(
                new Body($key),
                $closure,
            ),
        );
    }

    public function bodyQuery(string $key, Closure $closure): static
    {
        return $this->withValidator(
            $this->ruleValidatorFactory->create(
                new BodyQuery($key),
                $closure,
            ),
        );
    }

    public function query(string $key, Closure $closure): static
    {
        return $this->withValidator(
            $this->ruleValidatorFactory->create(
                new Query($key),
                $closure,
            ),
        );
    }

    public function header(string $key, Closure $closure): static
    {
        return $this->withValidator(
            $this->ruleValidatorFactory->create(
                new Header($key),
                $closure,
            ),
        );
    }

    public function route(string $key, Closure $closure): static
    {
        return $this->withValidator(
            $this->ruleValidatorFactory->create(
                new Route($key),
                $closure,
            ),
        );
    }

    public function condition(Closure $expressionValidatorsClosure, ?Closure $ifStatementValidatorsClosure = null, ?Closure $elseStatementValidatorsClosure = null): static
    {
        return $this->withValidator(
            new ConditionValidator(
                $this->buildValidators($expressionValidatorsClosure),
                $this->tryBuildValidators($ifStatementValidatorsClosure),
                $this->tryBuildValidators($elseStatementValidatorsClosure),
            ),
        );
    }

    public function compareDateTimes(Closure $closure): static
    {
        return $this->buildComparisonValidator(
            $closure,
            $this->dateTimeComparatorFactory->create(),
        );
    }

    public function compareFloats(Closure $closure): static
    {
        return $this->buildComparisonValidator(
            $closure,
            new FloatComparator(),
        );
    }

    public function compareInts(Closure $closure): static
    {
        return $this->buildComparisonValidator(
            $closure,
            new IntComparator(),
        );
    }

    public function compareStrings(Closure $closure): static
    {
        return $this->buildComparisonValidator(
            $closure,
            new StringComparator(),
        );
    }

    public function build(): array
    {
        return $this->validators;
    }

    protected function buildValidators(Closure $validatorsBuilderClosure): array
    {
        $validatorsBuilder = $validatorsBuilderClosure(
            $this->withoutValidators(),
        );
        if (!$validatorsBuilder instanceof ValidatorsBuilderInterface) {
            throw new ValidatorsBuilderException('not an instance of validators builder');
        }

        return $validatorsBuilder->build();
    }

    protected function tryBuildValidators(?Closure $validatorsBuilderClosure): array
    {
        if ($validatorsBuilderClosure === null) {
            return [];
        }

        return $this->buildValidators($validatorsBuilderClosure);
    }


    protected function buildComparisonValidator(Closure $comparisonValidatorBuilderClosure, ComparatorInterface $comparator): static
    {
        $comparisonValidatorBuilder = $comparisonValidatorBuilderClosure(
            $this->comparisonValidatorBuilder,
        );
        if (!$comparisonValidatorBuilder instanceof ComparisonValidatorBuilderInterface) {
            throw new ValidatorsBuilderException('not an instance of comparison builder');
        }

        return $this->withValidator(
            $comparisonValidatorBuilder
                ->withComparator($comparator)
                ->build()
        );
    }
}
