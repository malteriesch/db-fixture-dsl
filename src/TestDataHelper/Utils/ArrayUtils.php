<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Utils;


class ArrayUtils
{

    public static function splitByWhiteSpace(string $subject): array
    {
        preg_match_all('/"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"|([^\s"]+)/', $subject, $matches);
        $parts = array_map(function($part) {
            $part = trim($part, '"');
            $part = preg_replace('/\\\\(")/', '$1', $part);
            return $part;
        }, $matches[0]);
        return $parts;
    }

    public static function allWithKeys(array $subject, array $include): array
    {
        return array_intersect_key($subject, array_flip($include));
    }
}