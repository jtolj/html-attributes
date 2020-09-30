<?php

namespace Jtolj\HtmlAttributes;

use Jtolj\HtmlAttributes\Exceptions\InvalidValueException;
use Jtolj\HtmlAttributes\Helpers\Str;
use Laminas\Escaper\Escaper;

class HtmlAttributes
{
    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var array
     */
    protected $storage = [];

    /**
     * @var bool
     */
    protected $allow_unsafe;

    /**
     * @var string[]
     */
    protected $unsafe_prefixes = ['on'];

    /**
     * Constructor for HtmlAttributes.
     *
     * @param bool $allow_unsafe
     *
     * @return void
     */
    public function __construct(iterable $attributes = [], $allow_unsafe = false)
    {
        $this->allow_unsafe = $allow_unsafe;
        $this->escaper = new Escaper('utf-8');
        foreach ($attributes as $attribute => $value) {
            $this->setAttribute($attribute, $value);
        }
    }

    /**
     * Magic method to handle conditionals.
     *
     * Handles convenience methods for chaining in templates.
     * Example:
     * $html_attributes->addClassIf('even', $loop->even)
     *   Would add the class 'even' if (bool) $loop->even === true.
     * $html_attributes->addClassUnless('even, $loop->odd)
     *   Would add the class 'even' if (bool) $loop->odd === false.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return      */
    public function __call($name, $arguments)
    {
        if ($suffix = Str::extractSuffix($name, ['If', 'Unless'])) {
            $method = substr($name, 0, -strlen($suffix));
            $conditional = array_pop($arguments);
            $conditional_passed = is_callable($conditional) ? call_user_func($conditional) : (bool) $conditional;
            $should_run = $suffix === 'If' ? $conditional_passed : !$conditional_passed;

            if ($should_run) {
                call_user_func_array([$this, $method], $arguments);
            }

            return $this;
        }

        trigger_error('Call to undefined method '.__CLASS__.'::'.$name.'()', E_USER_ERROR);
    }

    /**
     * Renders the attributes as a string for template output.
     *
     * @return string
     */
    public function __toString()
    {
        $output = '';
        $attributes = $this->storage;

        if (!$this->allow_unsafe) {
            $attributes = array_filter($attributes, function ($name) {
                return !$this->isUnsafe($name);
            }, ARRAY_FILTER_USE_KEY);
        }

        // Sorting by attribute name provides predictable output for testing.
        ksort($attributes);

        foreach ($attributes as $attribute => $value) {
            if (!empty($output)) {
                $output .= ' ';
            }
            $output .= $this->render($attribute, $value);
        }

        return $output;
    }

    /**
     * Render an individual attribute.
     *
     * @param string       $attribute
     * @param string|array $value
     *
     * @return string
     *                The output for an individual attribute, example: class="c-card"
     */
    protected function render($attribute, $value)
    {
        $name = $attribute;

        $value = array_filter($value);
        $value = implode(' ', $value);
        $output_value = $this->isUnsafe($attribute) && $this->allow_unsafe ? $value : $this->escaper->escapeHtml($value);

        return sprintf('%s="%s"', $this->escaper->escapeHtmlAttr($name), $output_value);
    }

    /**
     * Get the value of storage at the specified offset.
     *
     * @param string $key
     *
     * @return array
     */
    public function offsetGet($key)
    {
        if (isset($this->storage[$key])) {
            return $this->storage[$key];
        }
    }

    /**
     * Set the value of storage at the specified offset.
     *
     * @param string $key
     * @param array  $value
     *
     * @return void;
     */
    protected function offsetSet($key, $value)
    {
        $this->storage[$key] = $value;
    }

    /**
     * Unset the specified offset.
     *
     * @param string $key
     *
     * @return void
     */
    protected function offsetUnset($key)
    {
        unset($this->storage[$key]);
    }

    /**
     * Whether the specified offset exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        return isset($this->storage[$key]);
    }

    /**
     * Return array of attributes and values.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->storage;
    }

    /**
     * Return a new instance with only the specified key(s).
     *
     * @param array|string $keys
     *
     * @return HtmlAttributes
     */
    public function only($keys)
    {
        if (!is_array($keys)) {
            $keys = [$keys];
        }

        $instance = new self([], $this->allow_unsafe);
        foreach ($keys as $key) {
            $instance->setAttribute($key, $this->offsetGet($key));
        }
        $instance->setUnsafePrefixes($this->unsafe_prefixes);

        return $instance;
    }

    /**
     * Return a new instance without the specified key(s).
     *
     * @param array|string $keys
     *
     * @return HtmlAttributes
     */
    public function without($keys)
    {
        if (!is_array($keys)) {
            $keys = [$keys];
        }

        $instance = new self($this->storage, $this->allow_unsafe);

        foreach ($keys as $key) {
            $instance->removeAttribute($key);
        }

        $instance->setUnsafePrefixes($this->unsafe_prefixes);

        return $instance;
    }

    /**
     * Set an attribute value, replacing previous value(s).
     *
     * @param string       $attribute
     * @param string|array $value
     *
     * @return HtmlAttributes
     */
    public function setAttribute($attribute, $value)
    {
        if (is_string($value) || (is_object($value) && method_exists($value, '__toString'))) {
            $this->offsetSet($attribute, [(string) $value]);
        } elseif (is_array($value)) {
            $this->offsetSet($attribute, (array) $value);
        } else {
            throw new InvalidValueException();
        }

        return $this;
    }

    /**
     * Add a value or values to an attribute, leaving existing values in place.
     *
     * @param string       $attribute
     * @param string|array $value
     *
     * @return HtmlAttributes
     */
    public function mergeAttributes($attribute, $value)
    {
        $values = $this->offsetGet($attribute);
        if (!is_array($values)) {
            $values = [$values];
        }
        if (is_string($value) || (is_object($value) && method_exists($value, '__toString'))) {
            $values[] = (string) $value;
        } elseif (is_array($value)) {
            $values = array_merge($values, (array) $value);
        } else {
            throw new InvalidValueException();
        }
        $this->offsetSet($attribute, array_values(array_unique($values)));

        return $this;
    }

    /**
     * Remove an attribute from the list.
     *
     * @return HtmlAttributes
     */
    public function removeAttribute(string $attribute)
    {
        $this->offsetUnset($attribute);
        unset($this->storage[$attribute]);

        return $this;
    }

    /**
     * Add $className to the class attribute.
     *
     * @param string|array $className
     *
     * @return HtmlAttributes
     */
    public function addClass($className)
    {
        $this->mergeAttributes('class', $className);

        return $this;
    }

    /**
     * Remove $className from the class attribute.
     *
     * @param string $className
     *
     * @return HtmlAttributes
     */
    public function removeClass($className)
    {
        $index = array_search($className, $this->storage['class']);
        if ($index > -1) {
            array_splice($this->storage['class'], $index, 1);
            if (empty($this->storage['class'])) {
                $this->removeAttribute('class');
            }
        }

        return $this;
    }

    /**
     * Checks if $className is in the class attribute.
     *
     * @param string $className
     *                          The class name to search for
     *
     * @return bool
     *              TRUE if the class exists, otherwise FALSE
     */
    public function hasClass($className)
    {
        if (isset($this->storage['class'])) {
            return in_array($className, $this->storage['class']);
        }

        return false;
    }

    /**
     * Setter for unsafe prefixes.
     *
     * @return void
     */
    public function setUnsafePrefixes(array $prefixes)
    {
        $this->unsafe_prefixes = $prefixes;

        return $this;
    }

    /**
     * Whether to allow "unsafe" attribute names (javascript event handlers) in the output.
     *
     * @return void
     */
    public function allowUnsafe(bool $allow = true)
    {
        $this->allow_unsafe = $allow;

        return $this;
    }

    /**
     * Whether the attribute name is "unsafe" (a javascript event handler).
     *
     * @return bool
     */
    protected function isUnsafe(string $attribute)
    {
        return array_reduce($this->unsafe_prefixes, static function ($is_unsafe, $prefix) use ($attribute) {
            if (strpos($attribute, $prefix) === 0) {
                $is_unsafe = true;
            }

            return $is_unsafe;
        }, false);
    }
}
