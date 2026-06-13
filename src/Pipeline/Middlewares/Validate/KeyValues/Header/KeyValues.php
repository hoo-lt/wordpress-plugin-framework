<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\KeyValues\Header;

use Hoo\WordPressPluginFramework\{
	Http\Server\Request\RequestInterface,
	Pipeline\Middlewares\Validate\KeyValues\KeyValuesInterface,
};

readonly class KeyValues implements KeyValuesInterface
{
	public function __construct(
		protected string $key,
	) {
	}

	public function key(): string
	{
		return $this->key;
	}

	public function values(RequestInterface $request): ?array
	{
		$headers = $request->headers();
		if ($headers === null) {
			return null;
		}

		return [
			$this->key => $headers->header($this->key),
		];
	}
}
