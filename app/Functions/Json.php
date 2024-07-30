<?php

namespace App\Functions;

class Json
{
    public static function format(string $json, int $indentSpaces = 4): string
    {
        /**
         * Copyright (c) 2018-2024 Andreas Möller
         *
         * https://github.com/ergebnis/json-printer
         */

        // Convert indent number to spaces
        $indent = str_repeat(' ', $indentSpaces);

        // Init
        $printed = null;
        $indentLevel = 0;
        $length = mb_strlen($json);
        $withinStringLiteral = false;
        $stringLiteral = '';
        $noEscape = true;

        for ($i = 0; $i < $length; ++$i) {
            // Grab the next character in the string.
            $character = mb_substr($json, $i, 1);

            // Are we inside a quoted string literal?
            if ('"' === $character && $noEscape) {
                $withinStringLiteral = !$withinStringLiteral;
            }

            // Collect characters if we are inside a quoted string literal.
            if ($withinStringLiteral) {
                $stringLiteral .= $character;
                $noEscape = '\\' === $character ? !$noEscape : true;

                continue;
            }

            // Process string literal if we are about to leave it.
            if ('' !== $stringLiteral) {
                $printed .= $stringLiteral . $character;
                $stringLiteral = '';

                continue;
            }

            // Ignore whitespace outside of string literal.
            if ('' === trim($character)) {
                continue;
            }

            // Ensure space after ":" character.
            if (':' === $character) {
                $printed .= ': ';

                continue;
            }

            // Output a new line after "," character and indent the next line.
            if (',' === $character) {
                $printed .= $character . "\n" . str_repeat($indent, $indentLevel);

                continue;
            }

            // Output a new line after "{" and "[" and indent the next line.
            if ('{' === $character || '[' === $character) {
                ++$indentLevel;

                $printed .= $character . "\n" . str_repeat($indent, $indentLevel);

                continue;
            }

            // Output a new line after "}" and "]" and indent the next line.
            if ('}' === $character || ']' === $character) {
                --$indentLevel;

                $trimmed = rtrim($printed);
                $previousNonWhitespaceCharacter = mb_substr($trimmed, -1);

                // Collapse empty {} and [].
                if ('{' === $previousNonWhitespaceCharacter || '[' === $previousNonWhitespaceCharacter) {
                    $printed = $trimmed . $character;

                    continue;
                }

                $printed .= "\n" . str_repeat($indent, $indentLevel);
            }

            $printed .= $character;
        }

        return $printed;
    }
}
