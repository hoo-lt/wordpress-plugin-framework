<?php

namespace Hoo\WordPressPluginFramework\View;

class View implements ViewInterface
{
	public function __construct(
		protected readonly string $path,
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
			extract($array);

			require($path);
		})($path, $array);

		return ob_get_clean();
	}
}