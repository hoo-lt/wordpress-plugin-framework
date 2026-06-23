<?php

namespace Hoo\WordPressPluginFramework\View;

use Hoo\WordPressPluginFramework\View\Model\ModelInterface;

interface ViewInterface
{
	public function model(): ?ModelInterface;
	
	public function render(): string;
}