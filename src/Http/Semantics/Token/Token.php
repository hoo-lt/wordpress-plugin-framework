<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Token;

readonly class Token implements TokenInterface
{
    public function __construct(
        protected string $token,
    ) {
        $this->validate($token);
    }

    public function __toString(): string
    {
        return $this->token;
    }

    protected function validate(string $token): void
    {
        if (preg_match('/\A[!#$%&\'*+\-.^_`|~0-9A-Za-z]++\z/', $token) !== 1) {
            throw new TokenException('not a valid token');
        }
    }
}
