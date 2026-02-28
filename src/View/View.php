<?php

namespace Hoo\WordpressPluginFramework\View;

class View implements ViewInterface
{
	public function __construct(
		protected readonly string $path = __DIR__ . '/../Views',
	) {
	}

	public function __invoke(string $view, array $array): string
	{
		$path = "{$this->path}{$view}/View.php";
		if (!file_exists($path)) {
			//throw exception here
		}

		ob_start();

		(static function ($path, $array) {
			extract($array);

			require($path);
		})($path, $array);

		return ob_get_clean();
	}
}