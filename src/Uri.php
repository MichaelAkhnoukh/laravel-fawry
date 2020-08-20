<?php

namespace Caishni\Fawry;

use Psr\Http\Message\UriInterface;

class Uri extends \GuzzleHttp\Psr7\Uri implements UriInterface
{
    private static $replaceQuery = ['&' => '%26'];

    private static function generateQueryString($key, $value)
    {
        // Query string separators ("=", "&") within the key or value need to be encoded
        // (while preventing double-encoding) before setting the query string. All other
        // chars that need percent-encoding will be encoded by withQuery().
        $queryString = strtr($key, self::$replaceQuery);

        if ($value !== null) {
            $queryString .= '=' . strtr($value, self::$replaceQuery);
        }

        return $queryString;
    }

    private static function getFilteredQueryString(UriInterface $uri, array $keys)
    {
        $current = $uri->getQuery();

        if ($current === '') {
            return [];
        }

        $decodedKeys = array_map('rawurldecode', $keys);

        return array_filter(explode('&', $current), function ($part) use ($decodedKeys) {
            return !in_array(rawurldecode(explode('=', $part)[0]), $decodedKeys, true);
        });
    }

    public static function withQueryValue(UriInterface $uri, $key, $value)
    {
        $result = self::getFilteredQueryString($uri, [$key]);

        $result[] = self::generateQueryString($key, $value);

        return $uri->withQuery(implode('&', $result));
    }

    public static function withQueryValues(UriInterface $uri, array $keyValueArray)
    {
        $result = self::getFilteredQueryString($uri, array_keys($keyValueArray));

        foreach ($keyValueArray as $key => $value) {
            $result[] = self::generateQueryString($key, $value);
        }

        return $uri->withQuery(implode('&', $result));
    }
}