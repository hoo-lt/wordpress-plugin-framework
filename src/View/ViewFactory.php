<?php

namespace Hoo\WordPressPluginFramework\View;

use Hoo\WordPressPluginFramework\{
    View\Model\ModelInterface,
    View\Renderer\RendererInterface,
};

readonly class ViewFactory implements ViewFactoryInterface
{
    public function __construct(
        protected RendererInterface $renderer,
        protected string $dir,
    ) {
    }

    public function create(string $view, ?ModelInterface $model = null): ViewInterface
    {
        $view = $this->tryCreate($view, $model);
        if ($view === null) {
            throw new ViewFactoryException("view not found");
        }

        return $view;
    }

    public function tryCreate(string $view, ?ModelInterface $model = null): ?ViewInterface
    {
        $file = $this->file($view);
        if ($file === null) {
            return null;
        }

        return new View($this->renderer, $file, $model);
    }

    protected function file(string $view): ?string
    {
        $dir = realpath($this->dir);
        if ($dir === false) {
            return null;
        }

        $file = realpath($dir . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $view) . '.php');
        if ($file === false) {
            return null;
        }

        return str_starts_with($file, $dir . DIRECTORY_SEPARATOR) ? $file : null;
    }
}
