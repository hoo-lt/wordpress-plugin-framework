<?php

namespace Hoo\WordPressPluginFramework\Helpers\KeyValue;

use InvalidArgumentException;
use stdClass;

/**
 * Type-deterministic key/value helper over array|object structures.
 *
 * Path syntax (Symfony PropertyAccess style):
 *   .name / name  → object property   (container must be an object)
 *   [key]         → array element     (container must be an array)
 *   .* / [*]      → wildcard over object properties / array elements
 *   \             → escape . [ ] inside a segment (e.g. [a\.b], [\*])
 *
 * Container type is never inferred: an accessor's shape declares the type,
 * missing intermediates are created as the type the next accessor demands,
 * and a shape that conflicts with existing data throws on write.
 */
readonly class Helper implements HelperInterface
{
	public function values(array|object $data, string $path): array
	{
		return $this->read($data, $this->parse($path), '');
	}

	public function value(array|object $data, string $path): mixed
	{
		$accessors = $this->parse($path);
		$values = $this->read($data, $accessors, '');

		foreach ($accessors as $accessor) {
			if ($accessor[2]) {
				return array_values($values);
			}
		}

		return $values === [] ? null : reset($values);
	}

	public function withValue(array|object $data, string $path, mixed $value): array|object
	{
		return $this->write($data, $this->parse($path), $value);
	}

	public function withoutValue(array|object $data, string $path): array|object
	{
		return $this->remove($data, $this->parse($path));
	}

	private function read(mixed $current, array $accessors, string $path): array
	{
		if ($accessors === []) {
			return [$path => $current];
		}

		$accessor = $accessors[0];
		$rest = array_slice($accessors, 1);

		if (!$this->matchesType($current, $accessor)) {
			return [];
		}

		if ($accessor[2]) {
			$values = [];
			foreach ($this->entries($current) as $key => $child) {
				$values += $this->read($child, $rest, $this->appendSegment($path, $accessor[0], (string) $key));
			}

			return $values;
		}

		if (!$this->has($current, $accessor)) {
			return [];
		}

		return $this->read($this->get($current, $accessor), $rest, $this->appendSegment($path, $accessor[0], $accessor[1]));
	}

	private function write(mixed $current, array $accessors, mixed $value): mixed
	{
		if ($accessors === []) {
			return $value;
		}

		$accessor = $accessors[0];
		$rest = array_slice($accessors, 1);

		if (!$this->matchesType($current, $accessor)) {
			throw new HelperException(
				sprintf('Path segment expects %s but found %s.', $accessor[0], get_debug_type($current))
			);
		}

		if ($accessor[2]) {
			$result = $current;
			foreach ($this->entries($current) as $key => $child) {
				$result = $this->set($result, $accessor[0], (string) $key, $this->write($child, $rest, $value));
			}

			return $result;
		}

		$child = $this->has($current, $accessor) ? $this->get($current, $accessor) : $this->create($rest);

		return $this->set($current, $accessor[0], $accessor[1], $this->write($child, $rest, $value));
	}

	private function create(array $rest): array|object|null
	{
		if ($rest === []) {
			return null;
		}

		return $rest[0][0] === 'object' ? new stdClass() : [];
	}

	private function remove(mixed $current, array $accessors): mixed
	{
		$accessor = $accessors[0];
		$rest = array_slice($accessors, 1);

		if (!$this->matchesType($current, $accessor)) {
			return $current;
		}

		if ($accessor[2]) {
			if ($rest === []) {
				return $this->emptyLike($current);
			}

			$result = $current;
			foreach ($this->entries($current) as $key => $child) {
				$result = $this->set($result, $accessor[0], (string) $key, $this->remove($child, $rest));
			}

			return $result;
		}

		if (!$this->has($current, $accessor)) {
			return $current;
		}

		if ($rest === []) {
			return $this->drop($current, $accessor[0], $accessor[1]);
		}

		return $this->set($current, $accessor[0], $accessor[1], $this->remove($this->get($current, $accessor), $rest));
	}

	private function matchesType(mixed $current, array $accessor): bool
	{
		return $accessor[0] === 'object' ? is_object($current) : is_array($current);
	}

	private function has(array|object $container, array $accessor): bool
	{
		return $accessor[0] === 'object'
			? property_exists($container, $accessor[1])
			: array_key_exists($accessor[1], $container);
	}

	private function get(array|object $container, array $accessor): mixed
	{
		return $accessor[0] === 'object'
			? $container->{$accessor[1]}
			: $container[$accessor[1]];
	}

	private function set(array|object $container, string $type, string $key, mixed $value): array|object
	{
		if ($type === 'object') {
			$clone = clone $container;
			$clone->{$key} = $value;

			return $clone;
		}

		$container[$key] = $value;

		return $container;
	}

	private function drop(array|object $container, string $type, string $key): array|object
	{
		if ($type === 'object') {
			$clone = clone $container;
			unset($clone->{$key});

			return $clone;
		}

		$isList = array_is_list($container);
		unset($container[$key]);

		return $isList ? array_values($container) : $container;
	}

	private function emptyLike(array|object $container): array|object
	{
		return is_object($container) ? new stdClass() : [];
	}

	private function entries(array|object $container): array
	{
		return is_object($container) ? get_object_vars($container) : $container;
	}

	private function appendSegment(string $path, string $type, string $key): string
	{
		return $type === 'object'
			? ($path === '' ? $key : $path . '.' . $key)
			: $path . '[' . $key . ']';
	}

	/**
	 * @return array<int, array{0: string, 1: string, 2: bool}>
	 */
	private function parse(string $path): array
	{
		if ($path === '') {
			throw new InvalidArgumentException('Path must not be empty.');
		}

		$accessors = [];
		$index = 0;
		$length = strlen($path);

		while ($index < $length) {
			$char = $path[$index];

			if ($char === '[') {
				[$key, $wildcard, $index] = $this->readIndex($path, $index + 1, $length);
				$accessors[] = ['array', $key, $wildcard];
				continue;
			}

			if ($char === '.') {
				if ($accessors === []) {
					throw new InvalidArgumentException(sprintf('Path "%s" must not start with ".".', $path));
				}

				[$key, $wildcard, $index] = $this->readProperty($path, $index + 1, $length);
				$accessors[] = ['object', $key, $wildcard];
				continue;
			}

			if ($accessors !== []) {
				throw new InvalidArgumentException(
					sprintf('Unexpected "%s" in path "%s"; a property after the first must be preceded by ".".', $char, $path)
				);
			}

			[$key, $wildcard, $index] = $this->readProperty($path, $index, $length);
			$accessors[] = ['object', $key, $wildcard];
		}

		return $accessors;
	}

	/**
	 * @return array{0: string, 1: bool, 2: int}
	 */
	private function readIndex(string $path, int $index, int $length): array
	{
		$key = '';
		$escaped = false;
		$closed = false;

		while ($index < $length) {
			$char = $path[$index];

			if ($char === '\\') {
				$index++;
				if ($index >= $length) {
					throw new InvalidArgumentException('Dangling escape in path.');
				}

				$key .= $path[$index];
				$escaped = true;
				$index++;
				continue;
			}

			if ($char === ']') {
				$closed = true;
				$index++;
				break;
			}

			$key .= $char;
			$index++;
		}

		if (!$closed) {
			throw new InvalidArgumentException('Unbalanced "[" in path.');
		}

		if ($key === '') {
			throw new InvalidArgumentException('Empty index "[]" in path.');
		}

		return [$key, $key === '*' && !$escaped, $index];
	}

	/**
	 * @return array{0: string, 1: bool, 2: int}
	 */
	private function readProperty(string $path, int $index, int $length): array
	{
		$key = '';
		$escaped = false;

		while ($index < $length) {
			$char = $path[$index];

			if ($char === '\\') {
				$index++;
				if ($index >= $length) {
					throw new InvalidArgumentException('Dangling escape in path.');
				}

				$key .= $path[$index];
				$escaped = true;
				$index++;
				continue;
			}

			if ($char === '.' || $char === '[') {
				break;
			}

			$key .= $char;
			$index++;
		}

		if ($key === '') {
			throw new InvalidArgumentException('Empty property in path.');
		}

		return [$key, $key === '*' && !$escaped, $index];
	}
}
