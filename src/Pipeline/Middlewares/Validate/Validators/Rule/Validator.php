<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rules;

use Closure;
use Hoo\WordPressPluginFramework\{
	Http\Server\Request\RequestInterface,
	Pipeline\Middlewares\Validate\KeyValue\KeyValueInterface,
	Pipeline\Middlewares\Validate\Validators\ValidatorInterface,
};

readonly class Validator implements ValidatorInterface
{
	public function __construct(
		protected KeyValueInterface $keyValue,
		protected array $rules = [],
	) {
	}

	public function validate(RequestInterface $request, Closure $closure): void
	{
		$values = $this->keyValue->values($request);
		if ($values === null) {
			$key = $this->keyValue->key();

			$closure($key, 'no content to validate');
		} else {
			foreach ($values as $key => $value) {
				foreach ($this->rules as $rule) {
					$rule($value, fn($message) => $closure($key, $message));
				}
			}
		}
	}
}
