<?php

namespace Hoo\WordPressPluginFramework\Helpers\Array;

readonly class Helper implements HelperInterface
{
	public function value(array $array, string $key): mixed
	{
		return $this->walkGet($array, explode('.', $key));
	}

	public function withValue(array $array, string $key, mixed $value): array
	{
		return $this->walkAdd($array, explode('.', $key), $value);
	}

	public function withoutValue(array $array, string $key): array
	{
		return $this->walkRemove($array, explode('.', $key));
	}

	/**
	 * @param list<string> $keys
	 */
	protected function walkGet(mixed $array, array $keys): mixed
	{
		if ($keys === []) {
			return $array;
		}

		$key = array_shift($keys);
		if ($key === '*') {
			if (!is_array($array)) {
				return null;
			}

			$result = [];
			foreach ($array as $item) {
				$result[] = $this->walkGet($item, $keys);
			}

			if (!in_array('*', $keys, true)) {
				return $result;
			}

			return array_merge([], ...array_filter($result, 'is_array'));
		}

		if (!is_array($array) || !array_key_exists($key, $array)) {
			return null;
		}

		return $this->walkGet($array[$key], $keys);
	}

	/**
	 * @param list<string> $keys
	 */
	protected function walkAdd(mixed $array, array $keys, mixed $value): mixed
	{
		if ($keys === []) {
			return $value;
		}

		$key = array_shift($keys);
		if ($key === '*') {
			if (!is_array($array)) {
				return [];
			}

			$result = [];
			foreach ($array as $index => $item) {
				if ($keys === []) {
					$result[$index] = $value;
					continue;
				}
				if (!is_array($item)) {
					$item = [];
				}
				$result[$index] = $this->walkAdd($item, $keys, $value);
			}
			return $result;
		}

		if (!is_array($array)) {
			$array = [];
		}

		if ($keys === []) {
			$array[$key] = $value;
			return $array;
		}

		$existing = $array[$key] ?? null;
		if (!is_array($existing)) {
			$existing = [];
		}

		$array[$key] = $this->walkAdd($existing, $keys, $value);
		return $array;
	}

	/**
	 * @param list<string> $keys
	 */
	protected function walkRemove(mixed $array, array $keys): mixed
	{
		if ($keys === []) {
			return $array;
		}
		if (!is_array($array)) {
			return $array;
		}

		$key = array_shift($keys);
		if ($key === '*') {
			$result = [];
			foreach ($array as $index => $item) {
				if ($keys === []) {
					continue;
				}
				$result[$index] = is_array($item)
					? $this->walkRemove($item, $keys)
					: $item;
			}
			return $result;
		}

		if (!array_key_exists($key, $array)) {
			return $array;
		}

		if ($keys === []) {
			unset($array[$key]);
			return $array;
		}

		$array[$key] = $this->walkRemove($array[$key], $keys);
		return $array;
	}
}
