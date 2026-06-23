<?php

namespace Hoo\WordPressPluginFramework\View\Renderer;

use Hoo\WordPressPluginFramework\View\Model\ModelInterface;

interface RendererInterface
{
	public function render(string $file, ?ModelInterface $model): string;
}