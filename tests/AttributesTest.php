<?php

namespace Jtolj\HtmlAttributes\Test;

use PHPUnit\Framework\TestCase;
use Jtolj\HtmlAttributes\HtmlAttributes;

class AttributesTest extends TestCase
{

    /** @test */
    public function it_is_initializable()
    {
        $attributes = new HtmlAttributes(['class' => ['card'], 'disabled' => 'disabled']);

        $this->assertInstanceOf(HtmlAttributes::class, $attributes);

        $this->assertContains('card', $attributes->offsetGet('class'));
        $this->assertTrue($attributes->hasClass('card'));
        $this->assertStringContainsString('class="card', (string) $attributes);

        $this->assertContains('disabled', $attributes->offsetGet('disabled'));
        $this->assertStringContainsString('disabled="disabled', (string) $attributes);
    }

    /** @test */
    public function it_can_have_an_attribute_added_to_it()
    {
        $attributes = new HtmlAttributes();
        $attributes->setAttribute('id', 'card');

        $this->assertContains('card', $attributes->offsetGet('id'));
        $this->assertStringContainsString('id="card"', (string) $attributes);
    }

    /** @test */
    public function it_can_have_an_attribute_removed_from_it()
    {
        $attributes = new HtmlAttributes();
        $attributes->setAttribute('id', 'card');
        $attributes->removeAttribute('id');

        $this->assertNull($attributes->offsetGet('id'));
        $this->assertStringNotContainsString('id', (string) $attributes);
    }

     /** @test */
    public function it_can_have_a_class_added_to_it()
    {
        $attributes = new HtmlAttributes();

        $attributes->addClass('card');

        $this->assertContains('card', $attributes->offsetGet('class'));
        $this->assertTrue($attributes->hasClass('card'));
        $this->assertStringContainsString('class="card"', (string) $attributes);
    }

     /** @test */
    public function it_can_have_a_class_removed_from_it()
    {
        $attributes = new HtmlAttributes(['class' => ['card', 'card--wide']]);

        $attributes->removeClass('card--wide');

        $this->assertContains('card', $attributes->offsetGet('class'));
        $this->assertNotContains('card--wide', $attributes->offsetGet('class'));
        $this->assertStringContainsString('class="card"', (string) $attributes);
        $this->assertStringNotContainsString('card--wide', (string) $attributes);
    }

     /** @test */
    public function it_can_have_all_classes_removed_from_it()
    {
        $attributes = new HtmlAttributes(['class' => ['card', 'card--wide']]);

        $attributes->removeClass('card--wide');
        $attributes->removeClass('card');

        $this->assertNull($attributes->offsetGet('class'));
        $this->assertStringNotContainsString('card', (string) $attributes);
        $this->assertStringNotContainsString('card--wide', (string) $attributes);
        $this->assertStringNotContainsString('class', (string) $attributes);
    }

    /** @test */
    public function it_can_disallow_unsafe_attribugtes()
    {
        $attributes = new HtmlAttributes(['onclick' => ['alert("hello");']]);

        $this->assertStringNotContainsString('onclick', (string) $attributes);

        $attributes = new HtmlAttributes(['onclick' => ['alert("hello");']]);
         $attributes->allowUnsafe();

        $this->assertStringContainsString('onclick', (string) $attributes);
    }
}
