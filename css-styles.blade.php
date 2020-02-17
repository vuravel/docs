@extends('docs.layout')

@section('doc-title', 'Vuravel CSS Styles docs')
@section('seo-title', 'Infinite possibilities and eternal re-use - 🎨')

@section('doc-content')

  <h2>CSS styles</h2>

<!-- ------------------------------------------------------------- -->
<h3>Form styles</h3>

<h4>Id and Class attributes</h4>

<p>
    Each form gets an automatically generated (unique) id and class that is infered from the Form class name. You may however force an id and class by adding a public `$id` and `$class` property to your <b>Form</b> class.
</p>

<pre><code class="language-php">&lt;?php

class MyForm extends <i>Form</i> 
{
   public $id = 'my-custom-form-id';

   public $class = 'my-custom-form-class'; 

   //...

}
</code></pre>

<!-- ------------------------------------------------------------- -->
<h4>Style classes and attribute</h4>

<p>
    To style the form element (meaning the wrapper), you have two options:
</p>

<ul>
    <li>Either set CSS classes in the `$class` public property.</li>
    <li>Or set the `$style` property as you would in the HTML attribute.</li>
</ul>

<pre><code class="language-php">&lt;?php

class MyForm extends <i>Form</i> 
{
   public $class = 'p-4 text-center'; //add padding

   public $style = 'min-height:500px;background:red'; //add custom CSS 

   //...

}
</code></pre>


<!-- ------------------------------------------------------------- -->
<h3>Components styles</h3>

<p>
    To style a specific component, you have multiple options.
</p>

<ol>
    <li>Either assign CSS classes using the `class` method or an id attribute using the `id` method.</li>
    <li>Or set the `$style` property as you would in the HTML attribute.</li>
    <li>Simply overriding or extending existing CSS classes.</li>
</ol>

<!-- ------------------------------------------------------------- -->
<h3>Themes: SCSS global styles</h3>

<p>
  The form styles are highly configurable in scss. You can change any of these variables before you import your chosen theme.
</p>

<pre><code class="language-scss">
/**
 * Spacing
 */
$form-field-margin-t: 1.2rem;
$form-field-margin-b: 1.2rem;

$form-element-margin-t: 1rem;
$form-element-margin-b: 1rem;

$form-control-padding-t: 0.4rem;
$form-control-padding-r: 0.7rem;
$form-control-padding-b: 0.4rem;
$form-control-padding-l: 0.7rem;
$form-control-padding: $form-control-padding-t $form-control-padding-r $form-control-padding-b $form-control-padding-l;

$form-label-margin-b: 0.25rem;

/**
 * Colors
 */
$form-primary-color: $primary;
$form-field-error-color: #f56565;
$form-control-bg: #fff;
$form-control-placeholder-color: gainsboro;

/**
 * Borders
 */
$form-control-border-radius: .25rem;
$form-control-border-color: gainsboro;
$form-control-border-color-focused: $form-primary-color;
$form-control-box-shadow-focused: 0 0 0.1rem 0.2rem mix($form-primary-color, #fff, 25);

$form-control-border: 1px solid $form-control-border-color;

/**
 * Icons
 */
$form-field-icon-width: 2.5rem;
$form-field-icon-color: gainsboro;

/**
 * Checkbox & Toggle
 */
$form-checkbox-height: 1.6rem;
$form-toggle-height: 1.6rem;
$form-toggle-width: 3rem;
</code></pre>

@endsection