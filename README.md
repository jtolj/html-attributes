# A Fluent Interface for Handling HTML Attributes in PHP

The package provides a simple class, inspired by Drupal's Drupal\Core\Template\Attribute, to help manage HTML attributes in a structured way.

I use it primarily in Laravel, so the pseudocode examples below use Laravel conventions. The package, however, is not specific to Laravel and can be used without it.

## Examples

### In a Controller

```php

use App\Post;
use Jtolj\HtmlAttributes\HtmlAttributes;

$post = Post::find(1);
$post->is_wide = true;
$attributes = new HtmlAttributes();
$attributes
    ->addClass('card');
    ->setAttribute('id', "post-{$post->id}");
$attributes->addClassIf('card--wide', $post->is_wide);

echo "<div $attributes>$post->escaped_content</div>"

```

### Output

```html
<div class="card card--wide" id="post-1">Hello World</div>
```

### Using the example Trait with a Eloquent model and Blade template:

```php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Jtolj\HtmlAttributes\Traits\HasHtmlAttributes;
class Post extends Model
{
    use HasHtmlAttributes;
}
```

```php
@foreach ($posts as $post)
    <div {!! $post->htmlAttributes()->addClass('card')->addClassIf('even', $loop->even) !!}>
    {{ $post->summary }}
    </div>
@endforeach
```

### Output

```html
<div class="card">First Post</div>
<div class="card even">Second Post</div>
<div class="card">Third Post</div>
```

## Escaping and Filtering

Escaping of attribute names and values is done using the laminas/laminas-escaper package. Attribute keys are escaped using the [escapeHtmlAttr()](https://github.com/laminas/laminas-escaper/blob/2.7.x/src/Escaper.php#L158) method. As of 2.0, attribute values are escaped using the [escapeHtml()](https://github.com/laminas/laminas-escaper/blob/2.7.x/src/Escaper.php#L145) method.

Additionally, by default attribute names starting with 'on' (javascript event handlers) are not output.

You can set your own list of stripped prefixes with the `setUnsafePrefixes(array $prefixes)` method. Attribute names beginning with those prefixes are stripped on output.

You can also turn this behavior off by calling `allowUnsafe()`. This will not filter the list of attribute names before output and will output the value of 'unsafe' attributes fully unescaped (as of 2.0). **Be extremely careful with this behavior to prevent XSS.**



```php
use Jtolj\HtmlAttributes\HtmlAttributes;

$attributes = new HtmlAttributes;
$attributes->addClass('card');
$attribute->setAttribute('onclick', 'alert("Hello";)');

$safe_string = (string) $attributes;
//class="card"

$attributes->allowUnsafe();
$unsafe_string = (string) $attributes;
//class="card" onclick="alert(\"Hello\";)"
```
