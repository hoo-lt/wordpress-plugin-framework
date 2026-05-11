<?php

namespace Hoo\WordPressPluginFramework\Helpers\KeyValue;

use InvalidArgumentException;

readonly class Helper implements HelperInterface
{
	public function values(array $array, string $key): array
	{
		$keys = $this->keys($key);
		$values = $this->valuesByKeys($array, $keys, []);

		return $values;
	}

	public function value(array $array, string $key): mixed
	{
		$keys = $this->keys($key);
		$values = $this->valuesByKeys($array, $keys, []);

		if (in_array('*', $keys, true)) {
			return array_values($values);
		}

		return reset($values);
	}

	public function withValue(array $array, string $key, mixed $value): array
	{
		return $this->withValueByKeys($array, $this->keys($key), $value);
	}

	public function withoutValue(array $array, string $key): array
	{
		return $this->withoutValueByKeys($array, $this->keys($key));
	}

	protected function keys(string $key): array
	{
		if ($key === '') {
			throw new InvalidArgumentException('Key must not be empty.');
		}

		$keys = explode('.', $key);
		if (in_array('', $keys, true)) {
			throw new InvalidArgumentException(
				sprintf('Key "%s" contains an empty key.', $key)
			);
		}

		return $keys;
	}

	protected function valuesByKeys(mixed $array, array $keys, array $path): array
	{
		if ($keys === []) {
			return [
				implode('.', $path) => $array
			];
		}

		$key = array_shift($keys);
		return $key === '*' ? $this->valuesByWildcardKey($array, $key, $keys, $path) : $this->valuesByKey($array, $key, $keys, $path);
	}

	protected function valuesByWildcardKey(mixed $array, string $key, array $keys, array $path): array
	{
		if (!is_array($array)) {
			return $this->missing($path, $key, $keys);
		}

		$values = [];

		foreach ($array as $key => $array) {
			$values += $this->valuesByKeys($array, $keys, [
				...$path,
				$key
			]);
		}

		return $values;
	}

	protected function valuesByKey(mixed $array, string $key, array $keys, array $path): array
	{
		if (
			!is_array($array) ||
			!array_key_exists($key, $array)
		) {
			return $this->missing($path, $key, $keys);
		}

		return $this->valuesByKeys($array[$key], $keys, [
			...$path,
			$key
		]);
	}

	protected function missing(array $path, string $key, array $keys): array
	{
		return [
			implode('.', [
				...$path,
				$key,
				...$keys
			]) => null
		];
	}

	protected function withValueByKeys(mixed $array, array $keys, mixed $value): mixed
	{
		if ($keys === []) {
			return $value;
		}

		$key = array_shift($keys);
		return $key === '*' ? $this->withValueByWildcardKey($array, $keys, $value) : $this->withValueByKey($array, $key, $keys, $value);
	}

	protected function withValueByWildcardKey(mixed $array, array $keys, mixed $value): mixed
	{
		if (!is_array($array)) {
			return $array;
		}

		$result = [];

		foreach ($array as $key => $child) {
			$result[$key] = $this->withValueByKeys($child, $keys, $value);
		}

		return $result;
	}

	protected function withValueByKey(mixed $array, string $key, array $keys, mixed $value): array
	{
		$array = is_array($array) ? $array : [];

		return array_replace($array, [
			$key => $this->withValueByKeys($array[$key] ?? null, $keys, $value)
		]);
	}

	protected function withoutValueByKeys(mixed $array, array $keys): mixed
	{
		if ($keys === []) {
			return $array;
		}

		$key = array_shift($keys);
		return $key === '*' ? $this->withoutValueByWildcardKey($array, $keys) : $this->withoutValueByKey($array, $key, $keys);
	}

	protected function withoutValueByWildcardKey(mixed $array, array $keys): mixed
	{
		if (!is_array($array)) {
			return $array;
		}

		if ($keys === []) {
			return [];
		}

		$result = [];

		foreach ($array as $key => $child) {
			$result[$key] = $this->withoutValueByKeys($child, $keys);
		}

		return $result;
	}

	protected function withoutValueByKey(mixed $array, string $key, array $keys): mixed
	{
		if (!is_array($array) || !array_key_exists($key, $array)) {
			return $array;
		}

		if ($keys === []) {
			return array_diff_key($array, [$key => null]);
		}

		return array_replace($array, [
			$key => $this->withoutValueByKeys($array[$key], $keys)
		]);
	}
}
