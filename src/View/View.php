<?php

namespace Hoo\WordPressPluginFramework\View;

readonly class View implements ViewInterface
{
	public function __construct(
		protected string $path,
	) {
	}

	public function __invoke(string $view, array $array): string
	{
		$path = "{$this->path}{$view}/View.php";
		if (!file_exists($path)) {
			throw new ViewException('view not found');
		}

		ob_start();

		(static function ($path, $array) {
			require($path);
		})($path, $array);

		return ob_get_clean();
	}
}