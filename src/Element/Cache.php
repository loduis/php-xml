<?php

namespace XML\Element;

use SimpleXMLElement;

class Cache
{
    private static $storage = [];

    public static function create(SimpleXMLElement $object)
    {
        $id = static::id($object);

        static::$storage[$id] = $object->getDocNamespaces(true);
    }

    public static function set(SimpleXMLElement $object, string $prefix, $value)
    {
        $id = static::id($object);

        static::$storage[$id][$prefix] = $value;
    }

    public function get(SimpleXMLElement $object, string $prefix)
    {
        $id = static::id($object);

        return static::$storage[$id][$prefix] ?? null;
    }

    private static function id(SimpleXMLElement $object)
    {
        return spl_object_hash($object);
    }
}
