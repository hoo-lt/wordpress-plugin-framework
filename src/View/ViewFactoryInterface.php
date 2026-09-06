<?php

namespace Hoo\WordPressPluginFramework\View;

use Hoo\WordPressPluginFramework\View\Model\ModelInterface;

interface ViewFactoryInterface
{
    public function create(string $view, ModelInterface $model): ViewInterface;
}
