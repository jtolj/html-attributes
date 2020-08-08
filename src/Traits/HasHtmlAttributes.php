<?php

namespace Jtolj\HtmlAttributes\Traits;

use Jtolj\HtmlAttributes\HtmlAttributes;

trait HasHtmlAttributes
{

    /*
     * @var Jtolj\HtmlAttributes\HtmlAttributes
     */
    protected $_html_attributes;

    /**
     * Accessor for htmlAttributes.
     *
     * @param iterable $attributes
     * @return HtmlAttributes
     */
    public function htmlAttributes(iterable $attributes = [])
    {
        if (!$this->_html_attributes) {
            $this->_html_attributes = new HtmlAttributes($attributes);
        }
        return $this->_html_attributes;
    }
}
