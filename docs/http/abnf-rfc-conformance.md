# ABNF vs. RFC — conformance audit

Line-by-line comparison of every constant in `src/Http/Abnf/` against the
grammar it cites. Three constants do **not** match their RFC rule exactly;
everything else is character-for-character equivalent.

Audited 2026-09-07 against `Rfc5234.php`, `Rfc6838.php`, `Rfc9110.php`.

## Mismatches

### 1. `Rfc9110::FIELD_CONTENT` — genuine grammar deviation

`src/Http/Abnf/Rfc9110.php:29`

```
RFC 9110 §5.5:  field-content = field-vchar [ 1*( SP / HTAB / field-vchar ) field-vchar ]
code:           FIELD_VCHAR (?:[WSP]* FIELD_VCHAR)*
```

In the ABNF the optional tail requires **at least two characters** (`1*(...)`
plus the trailing `field-vchar`), so the rule cannot derive a string of length
two. The regex can. Verified by exhaustive enumeration over the alphabet
`{vchar, wsp}` for lengths 0–6: the only difference is exactly `vchar vchar`.

Subtler, same line: the ABNF's inner `1*( SP / HTAB / field-vchar )` is a
single **mixed** run, whereas the regex hard-codes an alternation of
`whitespace → vchar`. No effect on the accepted language, but the structure is
not the RFC's.

### 2. `Rfc9110::FIELD_VALUE` — `*` collapsed into `?`

`src/Http/Abnf/Rfc9110.php:32`

```
RFC 9110 §5.5:  field-value = *field-content
code:           (?: FIELD_CONTENT )?
```

Literally not the same production. However `(?: code-field-content )?` and
`(?: rfc-field-content )*` accept the **identical language** (same enumeration,
empty diff) — precisely because the widening in §1 compensates for the lost
repetition.

So the pair 1+2 is correct *as a whole*, but neither constant equals its RFC
rule on its own. Anything that reuses `FIELD_CONTENT` apart from `FIELD_VALUE`
gets a rule the RFC does not define.

### 3. `Rfc9110::PARAMETERS` — the `[ parameter ]` optionality is gone

`src/Http/Abnf/Rfc9110.php:54`

```
RFC 9110 §5.6.6:  parameters = *( OWS ";" OWS [ parameter ] )
code:             (?<parameters> OWS ";" OWS PARAMETER )
```

Dropping the `*` is deliberate and documented — the caller supplies the
repetition via `preg_match_all`. The square brackets around `parameter`,
however, were dropped silently: per the RFC both `text/html;;charset=utf-8` and
a trailing `;` are legal, and neither produces a match here. Under
`preg_match_all` that is not an error but a **silent skip** — the scanner steps
over an empty segment without distinguishing it from garbage.

## Differs in spelling only — leave alone

- **`WEIGHT`: `[qQ]=` vs. `"q="`** — correct as written. ABNF string literals
  are case-insensitive (RFC 5234 §2.3), so `[qQ]` *is* the faithful
  translation, not a liberty.
- **`PARAMETER_VALUE`: branches swapped** (`quoted-string | token` instead of
  `token / quoted-string`). ABNF alternation is unordered and the branches have
  disjoint first characters (`"` is not a tchar), so this is equivalent.
- **`QVALUE`: `[0-9]` hard-coded** instead of `Rfc5234::DIGIT`, though it is
  the same `\x30-\x39`. The only place a core rule was not reused.

## Exact matches

All of `Rfc5234` (7 constants). All of `Rfc6838` — including `{0,126}` for
`*126restricted-name-chars` and both `=/` increments (`.` and `+`).

In `Rfc9110`: `OWS`, `TCHAR`, `TOKEN`, `OBS_TEXT`, `QDTEXT`, `QUOTED_PAIR`,
`QUOTED_STRING`, `FIELD_NAME`, `FIELD_VCHAR`, `QVALUE`, `TYPE`, `SUBTYPE`,
`PARAMETER_NAME`, `PARAMETER`, `WEIGHT`.

### Possessive quantifiers

`++` and `*+` were checked for safety and are sound everywhere: `TOKEN` always
terminates against a non-tchar (`/`, `=`, `;`, WSP), and inside `QUOTED_STRING`
the `qdtext` / `quoted-pair` branches are deterministic (`\x5C` is excluded
from both `%x23-5B` and `%x5D-7E`). No position requires backtracking.

## Outside `Abnf/`

`MediaRangeFactory` builds a media-range as `TYPE "/" SUBTYPE`, but RFC 9110
§12.5.1 defines `media-range = "*/*" / type "/*" / type "/" subtype`. Since `*`
is a valid tchar, `*/text` is accepted here although the RFC forbids it.
