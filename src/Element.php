<?php

namespace XML;

use DOMDocument;
use LengthException;
use SimpleXMLElement;
use XML\Element\Cache;
use InvalidArgumentException;

class Element extends SimpleXMLElement
{
    const XMLNS = 'http://www.w3.org/2000/xmlns/';

    private $_namespaces = [];

    public static function create($element, array $attributes = [], callable $callback = null)
    {
        $xml = static::getXML($element, $attributes);
        $element = new static($xml);
        if (is_callable($callback)) {
            $callback($element);
        }
        Cache::create($element);
        return $element;
    }

    public function add($element, $attributes = [], $callback = null)
    {
        $value = null;
        if (is_scalar($attributes)) {
            $value = $attributes;
            $attributes = [];
        } elseif (is_callable($attributes)) {
            $callback = $attributes;
            $attributes = [];
        } else if (is_scalar($callback)) {
            $value = $callback;
            $callback = null;
        }

        if ($element instanceof SimpleXMLElement) {
            return $this->fromElement($element);
        }

        $namespace = $this->resolveNamespace($element, $attributes);

        return $this->createChild(
            $element,
            $value,
            $namespace,
            $attributes,
            $callback
        );
    }

    public function toElement()
    {
        $dom = new DOMDocument('1.0');
        $element = dom_import_simplexml($this);
        $element = $dom->importNode($element, true);
        $element = $dom->appendChild($element);

        return $element;
    }

    public function toDocument()
    {
        $dom = new DOMDocument('1.0');
        $source = static::stripXmlns($this->asXML());
        $dom->loadXML($source, LIBXML_NSCLEAN);

        return $dom;
    }


    public function setValue($value)
    {
        $this[0] = $value;
    }

    public function pretty()
    {
        $doc = $this->toDocument();

        $doc->formatOutput = true;

        return $doc->saveXML() . PHP_EOL;
    }

    public function c14n()
    {
        return $this->toElement()->C14N();
    }

    public function __call($element, array $args)
    {
        $value = null;
        $attributes = [];
        $callback = null;
        $namespace = null;
        $count = count($args);
        if ($count) {
            if ($count > 3) {
                throw new LengthException('Max number of parameters: ' . $count);
            }
            $method = "resolveMethodWith{$count}Parameters";
            $this->$method(
                $args,
                $value,
                $namespace,
                $attributes,
                $callback
            );
        }

        return $this->createChild(
            $element,
            $value,
            $namespace,
            $attributes,
            $callback
        );
    }

    public function __toString()
    {
        return $this->toDocument()->saveXML();
    }

    protected function fromElement(SimpleXMLElement $element)
    {
        if (strlen(trim((string) $element))==0) {
            $xml = $this->addChild($element->getName());
            foreach($element->children() as $child) {
                $xml->fromElement($child);
            }
        } else {
            $xml = $this->addChild($element->getName(), (string) $element);
        }
        foreach($element->attributes() as $name => $value) {
            $xml->addAttribute($name, $value);
        }

        return $xml;
    }

    protected static function getXML($element, $attributes)
    {
        $source = "<$element";
        if ($attributes) {
            array_walk($attributes, function ($value, $key) use (& $source) {
                if ($value !== null) {
                    $source .= ' ' . $key . '="' . $value . '"';
                }
            });
        }

        return $source . "></$element>";
    }

    protected function resolveNamespace($element, & $attributes)
    {
        $prefix = static::getPart($element);
        foreach ($attributes as $key => $value) {
            if ($prefix && $prefix == static::getPart($key, 1)) {
                unset($attributes[$key]);
                if ($this->isXmlns($key)) {
                    Cache::set($this, $prefix, $value);
                }
                return $value;
            }
        }
        if ($prefix) {
            return Cache::get($this, $prefix);
        }
    }

    protected static function getPart($element, $position = 0)
    {
        $part = null;
        if (strpos($element, ':') !== false) {
            $parts = explode(':', $element, 2);
            $part = $parts[$position];
        }

        return $part;
    }

    protected function createChild($name, $value, $namespace, $attributes, $callback)
    {
        $element = $this->addChild($name, $value, $namespace);
        foreach ($attributes as $key => $value) {
            if ($value !== null) {
                $namespace = null;
                if ($isXmlns = $this->isXmlns($key)) {
                    $namespace = static::XMLNS;
                } elseif ($prefix = static::getPart($key)) {
                    $namespace = Cache::get($this, $prefix);
                }
                $element->addAttribute($key, $value, $namespace);
                if ($this->isXmlns($key)) {
                    Cache::set($this, static::getPart($key, 1), $value);
                }
            }
        }
        if (is_callable($callback)) {
            $callback($element);
        }

        return $element;
    }

    protected function resolveMethodWith1Parameters(
        $args,
        & $value,
        & $namespace,
        & $attributes,
        & $callback
    ){
        list ($param1) = $args;
        if (is_callable($param1)) {
            return $callback = $param1;
        }
        if (is_array($param1)) {
            return $attributes = $param1;
        }

        $value = $param1;
    }

    protected function resolveMethodWith2Parameters(
        $args,
        & $value,
        & $namespace,
        & $attributes,
        & $callback
    ){
        list ($param1, $param2) = $args;
        if (is_scalar($param1)) {
            if (is_scalar($param2)) {
                $value = $param1;
                return $namespace = $param2;
            }
            if (is_array($param2)) {
                $value = $param1;
                return $attributes = $param2;
            }
            if (is_callable($param2)) {
                $namespace = $param1;
                return $callback = $param2;
            }
            $this->throwInvalidArgument();
        }

        if (is_array($param1)) {
            if (is_scalar($param2)) {
                $attributes = $param1;
                return $namespace = $param2;
            }
            if (is_callable($param2)) {
                $attributes = $param1;
                return $callback = $param2;
            }
        }
        $this->throwInvalidArgument();
    }

    protected function resolveMethodWith3Parameters(
        $args,
        & $value,
        & $namespace,
        & $attributes,
        & $callback
    ){
        list ($param1, $param2, $param3) = $args;
        if (is_scalar($param1) && is_array($param2) && is_callable($param3)) {
            $namespace = $param1;
            $attributes = $param2;
            $callback = $param3;
        }
        $this->throwInvalidArgument();
    }

    protected function throwInvalidArgument()
    {
        throw new InvalidArgumentException(
            'This is not valid arguments conbination'
        );
    }

    protected function isXmlns($value)
    {
        return stripos($value, 'xmlns:') === 0;
    }

    private static function stripXmlns(string $string)
    {
        return str_replace(
            'xmlns:xmlns="' . static::XMLNS .'" ',
            '',
            $string
        );
    }
}
