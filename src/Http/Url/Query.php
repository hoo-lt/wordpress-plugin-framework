<?php

namespace Hoo\WordPressPluginFramework\Http\Url;

readonly class Query implements QueryInterface
{
	protected function __construct(
		protected array $values,
	) {
	}

	public static function from(string $query): QueryInterface
	{
		return new self(
			self::parse($query)
		);
	}

	public function value(string $key): string
	{
		return $this->values[$key];
	}

	public function withValue(string $key, string $value): QueryInterface
	{
		return new self(
			array_filter(array_merge($this->values, [
				$key => $value,
			]))
		);
	}

	public function __toString(): string
	{
		return http_build_query($this->values);
	}

	protected static function parse(string $query): array
	{
		parse_str($query, $query);

		return $query;
	}
}
