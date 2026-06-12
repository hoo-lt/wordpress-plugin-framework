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

	public function withValues(array $values): static
	{
		return new static($this->path, $values);
	}

	public function withoutValues(): static
	{
		return new static($this->path, []);
	}

	public function withValue(string $key, mixed $value): static
	{
		$values = $this->values;
		$values[$key] = $value;

		return new static($this->path, $values);
	}

	public function withoutValue(string $key): static
	{
		$values = $this->values;
		unset($values[$key]);

		return new static($this->path, $values);
	}

	public function has(string $view): bool
	{
		$viewPath = $this->viewPath($view);
		$viewsPath = $this->viewsPath();

		$viewRealpath = realpath($viewPath);
		$viewsRealpath = realpath($viewsPath);

		if (
			$viewRealpath === false ||
			$viewsRealpath === false
		) {
			return false;
		}

		return str_starts_with($viewRealpath, $viewsRealpath);
	}

	public function get(string $view): string
	{
		if (!$this->has($view)) {
			throw new ViewException('invalid view path');
		}

		$viewPath = $this->viewPath($view);

		ob_start();

		try {
			(static function ($viewPath, $values) {
				extract($values, EXTR_SKIP);

				require($viewPath);
			})($viewPath, $this->values);
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

	protected function viewPath(string $view): string
	{
		return $this->viewsPath() . str_replace('.', '/', $view) . '.php';
	}

	protected function viewsPath(): string
	{
		return "{$this->path}/views/";
	}
}