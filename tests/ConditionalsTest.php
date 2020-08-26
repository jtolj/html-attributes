<?php

namespace Jtolj\HtmlAttributes\Test;

use PHPUnit\Framework\TestCase;
use Jtolj\HtmlAttributes\HtmlAttributes;

class ConditionalsTest extends TestCase
{

    /** @test */
    public function set_attribute_works_with_if_when_callable()
    {
        $attributes = new HtmlAttributes();

        $attributes->setAttributeIf('id', 'card', function () {
            return true;
        });

        $this->assertEquals('id="card"', (string) $attributes);

        $attributes = new HtmlAttributes();

        $attributes->setAttributeIf('id', 'card', function () {
            return false;
        });

        $this->assertEquals('', (string) $attributes);
    }

    /** @test */
    public function set_attribute_works_with_if_when_boolean()
    {
        $attributes = new HtmlAttributes();

        $attributes->setAttributeIf('id', 'card', true);

        $this->assertEquals('id="card"', (string) $attributes);

        $attributes = new HtmlAttributes();

        $attributes->setAttributeIf('id', 'card', false);

        $this->assertEquals('', (string) $attributes);
    }

    /** @test */
    public function set_attribute_works_with_unless_when_callable()
    {
        $attributes = new HtmlAttributes();

        $attributes->setAttributeUnless('id', 'card', function () {
            return true;
        });

        $this->assertEquals('', (string) $attributes);

        $attributes = new HtmlAttributes();

        $attributes->setAttributeUnless('id', 'card', function () {
            return false;
        });

        $this->assertEquals('id="card"', (string) $attributes);
    }

    /** @test */
    public function set_attribute_works_with_unless_when_boolean()
    {
        $attributes = new HtmlAttributes();

        $attributes->setAttributeUnless('id', 'card', true);

        $this->assertEquals('', (string) $attributes);

        $attributes = new HtmlAttributes();

        $attributes->setAttributeUnless('id', 'card', false);

        $this->assertEquals('id="card"', (string) $attributes);
    }
}
