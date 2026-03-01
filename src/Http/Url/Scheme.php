<?php

namespace Hoo\WordPressPluginFramework\Http\Url;

enum Scheme: string
{
	case Http = 'http';
	case Https = 'https';
}