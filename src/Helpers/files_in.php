<?php

/**
 * Files in
 *
 * @param  string|null  $search For example '/\.php$/'
 * @param  string  $excludePattern For example '/vendor/'
 */
function files_in(string $path, ?string $search = null, ?string $excludePattern = null): Generator
{
    if (! is_dir($path)) {
        throw new \RuntimeException("{$path} is not a directory ");
    }

    $it = new \RecursiveDirectoryIterator($path);
    $it = new \RecursiveIteratorIterator($it);

    if ($search) {
        // Replace space by a pipe
        $search = str_replace('*', '4ST3R1ZK', $search);
        $search = '/'.preg_quote($search, '/').'/i';
        $search = str_replace('4ST3R1ZK', '.*', $search);

        $it = new \RegexIterator($it, $search, \RegexIterator::MATCH);
    }

    if ($excludePattern) {
        $it = new \RegexIterator($it, $excludePattern, \RegexIterator::MATCH, \RegexIterator::INVERT_MATCH);
    }

    yield from $it;
}

function files_list(string $path, ?string $search = null, ?string $excludePattern = null): array
{
    return iterator_to_array(files_in($path, $search, $excludePattern));
}
