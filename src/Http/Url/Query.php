<?php

namespace Hoo\WordPressPluginFramework\Http\Url;

readonly class Query implements QueryInterface
{
	protected function __construct(
		protected array $query,
	) {
	}

	public static function from(array $query): QueryInterface
	{
		return new self(
			$query,
		);
	}

	public function withValue(string $name, string $value): QueryInterface
	{
		return new self(
			array_filter(array_merge($this->query, [
				$name => $value,
			]))
		);
	}

	public function __toString(): string
	{
		return http_build_query($this->query);
	}
}
