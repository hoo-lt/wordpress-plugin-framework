<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Values\Header;

use Hoo\WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Pipeline\Middlewares\Validate\Values\ValuesInterface,
};

readonly class Values implements ValuesInterface
{
	public function __construct(
		protected string $key,
	) {
	}

	public function __invoke(RequestInterface $request): array
	{
		$headers = $request->headers();
		if ($headers === null) {
			return [];
		}

		return [
			$this->key => $headers->header($this->key),
		];
	}
}
