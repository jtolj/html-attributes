<?php

namespace Jtolj\HtmlAttributes\Traits;

use Jtolj\HtmlAttributes\HtmlAttributes;

trait HasHtmlAttributes
{

    /**
     * Accessor for htmlAttributes.
     *
     * @param iterable $attributes
     * @return HtmlAttributes
     */
    public function htmlAttributes(iterable $attributes = [])
    {
        return new HtmlAttributes($attributes);
    }
}
