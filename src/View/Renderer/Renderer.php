<?php

namespace Hoo\WordPressPluginFramework\View\Renderer;

use Hoo\WordPressPluginFramework\{
	View\Model\ModelInterface,
	View\Renderer\Escaper\EscaperInterface,
};
use Throwable;

readonly class Renderer implements RendererInterface
{
	public function __construct(
		protected EscaperInterface $escaper,
	) {
	}

	public function render(string $file, ?ModelInterface $model): string
	{
		if (!file_exists($file)) {
			throw new RendererException('file not found');
		}

		ob_start();

		try {
			self::require($this->escaper, $file, $model);
		} catch (Throwable $throwable) {
			ob_end_clean();

			throw $throwable;
		}

		$ob = ob_get_clean();
		if ($ob === false) {
			throw new RendererException('Failed to capture view output');
		}

		return $ob;
	}

	protected static function require(EscaperInterface $escaper, string $file, ?ModelInterface $model): void
	{
		require($file);
	}
}