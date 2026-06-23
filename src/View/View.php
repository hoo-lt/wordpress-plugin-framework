<?php

namespace Hoo\WordPressPluginFramework\View;

use Hoo\WordPressPluginFramework\{
	View\Model\ModelInterface,
	View\Renderer\RendererInterface,
};

readonly class View implements ViewInterface
{
	public function __construct(
		protected RendererInterface $renderer,
		protected string $file,
		protected ?ModelInterface $model,
	) {
	}

	public function model(): ?ModelInterface
	{
		return $this->model;
	}

	public function render(): string
	{
		return $this->renderer->render($this->file, $this->model);
	}
}
