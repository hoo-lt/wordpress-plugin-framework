<?php

namespace Hoo\WordPressPluginFramework\Http\Request;

use Hoo\WordPressPluginFramework\Http\Method\Method;
use Hoo\WordPressPluginFramework\Http\Request\Body\Parser\ParserInterface;

readonly class Request implements RequestInterface
{
	protected ?array $parsedBody;

	public function __construct(
		protected ParserInterface $parser,
		protected array $server,
		protected array $get,
	) {
		$this->parsedBody = ($this->parser)();
	}

	public function method(): Method
	{
		return Method::from($this->server['REQUEST_METHOD']);
	}

	public function contentType(): ?string
	{
		return ($this->server['CONTENT_TYPE'] ?? null) ?: null;
	}

	public function query(?string $key = null): mixed
	{
		if ($key === null) {
			return $this->get;
		}

		return $this->value($key, $this->get);
	}

	public function body(?string $key = null): mixed
	{
		if ($key === null) {
			return $this->parsedBody;
		}

		if ($this->parsedBody === null) {
			return null;
		}

		return $this->value($key, $this->parsedBody);
	}

	public function bodyValues(string $key): array
	{
		if ($this->parsedBody === null) {
			return [];
		}

		return $this->resolve(explode('.', $key), $this->parsedBody, '');
	}

	public function queryValues(string $key): array
	{
		return $this->resolve(explode('.', $key), $this->get, '');
	}

	protected function value(string $key, array $array): mixed
	{
		foreach (explode('.', $key) as $key) {
			if (
				!is_array($array) ||
				!array_key_exists($key, $array)
			) {
				return null;
			}

			$array = $array[$key];
		}

		return $array;
	}

	protected function resolve(array $segments, mixed $data, string $prefix): array
	{
		if (empty($segments)) {
			return [$prefix => $data];
		}

		$segment = array_shift($segments);

		if ($segment === '*') {
			if (!is_array($data)) {
				return [];
			}

			$results = [];
			foreach ($data as $index => $item) {
				$newPrefix = $prefix === '' ? (string) $index : "{$prefix}.{$index}";
				$results += $this->resolve($segments, $item, $newPrefix);
			}

			return $results;
		}

		if (!is_array($data) || !array_key_exists($segment, $data)) {
			return [];
		}

		$newPrefix = $prefix === '' ? $segment : "{$prefix}.{$segment}";

		return $this->resolve($segments, $data[$segment], $newPrefix);
	}
}