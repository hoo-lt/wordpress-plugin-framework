<?php

namespace Hoo\WordPressPluginFramework\View;

use Throwable;

readonly class View implements ViewInterface
{
	public function __construct(
		protected string $path,
		protected array $values = [],
	) {
	}

	public function withValue(string $key, mixed $value): ViewInterface
	{
		return new self(
			$this->path,
			[
				...$this->values,
				$key => $value,
			]
		);
	}

	public function __invoke(string $view): string
	{
		$path = "{$this->path}/views/" . strtr($view, [
			'.' => '/',
		]) . '.php';

		$realpath = realpath($path);
		if (
			$realpath === false ||
			!str_starts_with($realpath, realpath("{$this->path}/views/"))
		) {
			throw new ViewException('invalid view path');
		}

		ob_start();

		try {
			(static function ($path, $values) {
				extract($values, EXTR_SKIP);

				require($path);
			})($path, $this->values);
		} catch (Throwable $throwable) {
			ob_end_clean();

			throw $throwable;
		}

		$ob = ob_get_clean();
		if ($ob === false) {
			throw new ViewException('Failed to capture view output');
		}

		return $ob;
	}
}