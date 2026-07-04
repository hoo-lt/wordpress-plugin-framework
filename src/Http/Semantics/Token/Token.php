<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Token;

readonly class Token implements TokenInterface
{
    /**
     * token = 1*tchar per RFC 9110 section 5.6.2
     *
     * tchar = "!" / "#" / "$" / "%" / "&" / "'" / "*" / "+" / "-" / "." /
     *         "^" / "_" / "`" / "|" / "~" / DIGIT / ALPHA
     */
    public const PATTERN = '[!#$%&\'*+\-.^_`|~0-9A-Za-z]++';

    public function __construct(
        protected string $token,
    ) {
        $this->validate($token);
    }

    public function value(): string
    {
        return $this->token;
    }

    public function __toString(): string
    {
        return $this->token;
    }

    protected function validate(string $token): void
    {
        if (preg_match('/\A' . static::PATTERN . '\z/', $token) !== 1) {
            throw new TokenException('not a valid token');
        }
    }
}
