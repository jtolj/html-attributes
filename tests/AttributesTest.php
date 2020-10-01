<?php

namespace Jtolj\HtmlAttributes\Test;

use Jtolj\HtmlAttributes\HtmlAttributes;
use PHPUnit\Framework\TestCase;

class AttributesTest extends TestCase
{
    /** @test */
    public function it_is_initializable()
    {
        $attributes = new HtmlAttributes(['class' => ['card'], 'disabled' => 'disabled']);

        $this->assertInstanceOf(HtmlAttributes::class, $attributes);

        $this->assertContains('card', $attributes->offsetGet('class'));
        $this->assertTrue($attributes->hasClass('card'));
        $this->assertContains('disabled', $attributes->offsetGet('disabled'));
        $this->assertEquals('class="card" disabled="disabled"', (string) $attributes);
    }

    /** @test */
    public function it_can_have_an_attribute_added_to_it()
    {
        $attributes = new HtmlAttributes();
        $attributes->setAttribute('id', 'card');

        $this->assertContains('card', $attributes->offsetGet('id'));
        $this->assertEquals('id="card"', (string) $attributes);
    }

    /** @test */
    public function it_can_have_an_attribute_removed_from_it()
    {
        $attributes = new HtmlAttributes();
        $attributes->setAttribute('id', 'card');
        $attributes->removeAttribute('id');

        $this->assertNull($attributes->offsetGet('id'));
        $this->assertEquals('', (string) $attributes);
    }

    /** @test */
    public function it_can_have_classes_added_to_it()
    {
        $attributes = new HtmlAttributes();

        $attributes->addClass('card');
        $attributes->addClass('card--wide');

        $this->assertContains('card', $attributes->offsetGet('class'));
        $this->assertTrue($attributes->hasClass('card'));
        $this->assertEquals('class="card card--wide"', (string) $attributes);
    }

    /** @test */
    public function it_can_have_a_class_removed_from_it()
    {
        $attributes = new HtmlAttributes(['class' => ['card', 'card--wide']]);

        $attributes->removeClass('card--wide');

        $this->assertContains('card', $attributes->offsetGet('class'));
        $this->assertNotContains('card--wide', $attributes->offsetGet('class'));
        $this->assertEquals('class="card"', (string) $attributes);
    }

    /** @test */
    public function it_can_have_all_classes_removed_from_it()
    {
        $attributes = new HtmlAttributes(['class' => ['card', 'card--wide']]);

        $attributes->removeClass('card--wide');
        $attributes->removeClass('card');

        $this->assertNull($attributes->offsetGet('class'));
        $this->assertEquals('', (string) $attributes);
    }

    /** @test */
    public function it_can_have_attributes_merged_into_it()
    {
        $attributes = new HtmlAttributes(['class' => ['card', 'card--wide'], 'id' => 'card-1']);
        $attributes_copy = new HtmlAttributes(['class' => ['card', 'card--wide'], 'id' => 'card-1']);

        // Test String
        $attributes->mergeAttributes('aria-description', 'A description.');
        $attributes_copy->merge('aria-description', 'A description.');

        $this->assertEquals((string) $attributes, (string) $attributes_copy);
        $this->assertEquals('aria-description="A description." class="card card--wide" id="card-1"', (string) $attributes);

        // Test String Classes with Spaces
        $attributes->mergeAttributes('class', 'one two three');
        $attributes_copy->merge('class', 'one two three');

        $this->assertEquals((string) $attributes, (string) $attributes_copy);
        $this->assertEquals('aria-description="A description." class="card card--wide one two three" id="card-1"', (string) $attributes);

        // Test Array
        $attributes->mergeAttributes('class', ['red', 'blue', 'green']);
        $attributes_copy->merge('class', ['red', 'blue', 'green']);
        $this->assertEquals((string) $attributes, (string) $attributes_copy);
        $this->assertEquals('aria-description="A description." class="card card--wide one two three red blue green" id="card-1"', (string) $attributes);
    }

    /** @test */
    public function it_can_disallow_unsafe_attributes()
    {
        $attributes = new HtmlAttributes(['onclick' => ['alert("hello");']]);

        $this->assertEquals('', (string) $attributes);
    }

    /** @test */
    public function it_can_allow_unsafe_attributes()
    {
        $attributes = new HtmlAttributes(['onclick' => ['alert("hello");']]);
        $attributes->allowUnsafe();

        $this->assertEquals('onclick="alert("hello");"', (string) $attributes);
    }

    /** @test */
    public function it_can_return_a_subset_of_keys_from_a_string()
    {
        $attributes = new HtmlAttributes(['class' => ['one', 'two', 'three'], 'id' => 'card']);

        $only_classes = $attributes->only('class');

        $this->assertEquals('class="one two three"', (string) $only_classes);
    }

    /** @test */
    public function it_can_return_a_subset_of_keys_from_an_array()
    {
        $attributes = new HtmlAttributes(['class' => ['one', 'two', 'three'], 'id' => 'card', 'for' => 'carditem']);

        $only_class_and_id = $attributes->only(['id', 'class']);

        $this->assertEquals('class="one two three" id="card"', (string) $only_class_and_id);
    }

    /** @test */
    public function it_can_exclude_a_key_from_a_string()
    {
        $attributes = new HtmlAttributes(['class' => ['one', 'two', 'three'], 'id' => 'card']);

        $only_id = $attributes->without('id');

        $this->assertEquals('class="one two three"', (string) $only_id);
    }

    /** @test */
    public function it_can_exclude_a_subset_of_keys_from_an_array()
    {
        $attributes = new HtmlAttributes(['class' => ['one', 'two', 'three'], 'id' => 'card', 'for' => 'carditem']);

        $only_for = $attributes->without(['id', 'class']);

        $this->assertEquals('for="carditem"', (string) $only_for);
    }
}
