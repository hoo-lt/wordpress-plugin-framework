<?php

namespace Hoo\WordPressPluginFramework\Renderer;

use Hoo\WordPressPluginFramework\View\ViewInterface;

interface RendererInterface
{
	public function render(ViewInterface $view): string;
}
