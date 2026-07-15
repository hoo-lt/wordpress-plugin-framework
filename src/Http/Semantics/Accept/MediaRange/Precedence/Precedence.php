<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Accept\MediaRange\Precedence;

enum Precedence: int
{
    case TypeSubtypeParameters = 1;
    case TypeSubtype = 2;
    case TypeWildcardSubtype = 3;
    case WildcardType = 4;
}
