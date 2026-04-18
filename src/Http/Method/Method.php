<?php

namespace Hoo\WordPressPluginFramework\Http\Method;

enum Method: string
{
	case Get = 'GET';
	case Head = 'HEAD';
	case Post = 'POST';
	case Put = 'PUT';
	case Patch = 'PATCH';
	case Delete = 'DELETE';
	case Options = 'OPTIONS';
}
