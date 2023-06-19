<?php

/**
 * Files in
 *
 * @param string $path
 * @param string|null $search For example '/\.php$/'
 * @param string $excludePattern For example '/vendor/'
 * @return Generator
 */
function files_in(string $path, ?string $search = null, ?string $excludePattern = null): Generator
{
    if (! is_dir($path)) {
        throw new \RuntimeException("{$path} is not a directory ");
    }

    $it = new \RecursiveDirectoryIterator($path);
    $it = new \RecursiveIteratorIterator($it);

    // $it = new \RegexIterator($it, $pattern, \RegexIterator::MATCH);
    if ($search) {
        // Replace space by a pipe
        $search = '/' . preg_quote($search, '/') . '/i';
        $search = str_replace(' ', '.*', $search);

        $it = new \RegexIterator($it, $search, \RegexIterator::MATCH);
    }

    if ($excludePattern) {
        $it = new \RegexIterator($it, $excludePattern, \RegexIterator::MATCH, \RegexIterator::INVERT_MATCH);
    }

    yield from $it;
}

function files_list(string $path, ?string $search = null, ?string $excludePattern = null): array {
    return iterator_to_array(files_in($path, $search, $excludePattern));
}