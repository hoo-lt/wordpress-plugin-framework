<?php

namespace Hoo\WordPressPluginFramework\View;

use Hoo\WordPressPluginFramework\View\Model\ModelInterface;

interface ViewFactoryInterface
{
    public function create(string $view, ?ModelInterface $model = null): ViewInterface;
    public function tryCreate(string $view, ?ModelInterface $model = null): ?ViewInterface;
}
