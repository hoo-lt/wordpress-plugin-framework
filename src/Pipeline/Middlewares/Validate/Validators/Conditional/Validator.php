<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\If;

use Closure;
use Hoo\WordPressPluginFramework\{
	Collections\Message\Collection as MessageCollection,
	Http\Server\Request\RequestInterface,
	Pipeline\Middlewares\Validate\Validators\ValidatorInterface,
};

readonly class Validator implements ValidatorInterface
{
	public function __construct(
		protected array $expressionValidators,
		protected array $statementValidators,
	) {
	}

	public function validate(RequestInterface $request, Closure $closure): void
	{
		$messages = new MessageCollection();

		foreach ($this->expressionValidators as $expressionValidator) {
			$expressionValidator->validate(
				$request,
				$messages->add(...),
			);
		}

		if ($messages->isNotEmpty()) {
			return;
		}

		foreach ($this->statementValidators as $statementValidator) {
			$statementValidator->validate($request, $closure);
		}
	}
}
