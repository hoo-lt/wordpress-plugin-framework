<?php

namespace Hoo\WordPressPluginFramework\View;

use Hoo\WordPressPluginFramework\View\Model\ModelInterface;

interface ViewInterface
{
	public function file(): string;
	public function model(): ModelInterface;
}
