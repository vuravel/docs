@extends('docs.layout')

@section('doc-title', 'Vuravel menus docs')
@section('seo-title', 'Navbars, Sidebars, Footers & Other responsive Menu items ¯\_(ツ)_/¯')

@section('doc-content')

<h2>Default blade template</h2>

<p>
  Vuravel comes with a default blade template `vuravel-menu::app` in which you can specify menus into slots that will display like this: 
</p>

<default-blade-template :form="{{ new App\Forms\DefaultBladeTemplateForm() }}"></default-blade-template>

<p>
  You may extend the default template as many times as you wish by specifying which menus, styles or scripts each template will use.
</p>

<h3>Displaying the menus</h3>

<p>
  For menus, you may extend the default template by injecting the following available slots `Navbar`, `LeftSidebar`, `RightSidebar` & `Footer`, which are all optional.
</p>

<pre><code class="language-php">//In your app.blade.php
&#64;extends('vuravel-menu::app', [
  'Navbar' => new App\Menus\Navbar(), 
  'LeftSidebar' => new App\Menus\LeftSidebar(), 
  'RightSidebar' => new App\Menus\RightSidebar(), 
  'Footer' => new App\Menus\Footer()
])

&#64;push('header')
   //include additional headers
&#64;endpush

&#64;section('content')
   //include content
&#64;endsection

&#64;push('scripts')
   //include additional scripts
&#64;endpush</code></pre>

<h3>Custom Styles & Scripts</h3>

<p>As shown above, you may add any kind of content in the `&lt;header>` section or at the bottom of your document in the `scripts` section thanks to Blade's `push` directive, which allows us to stack content in our HTML document. For example:
</p>

<pre><code class="language-html">`&#64;push`('header')
   &lt;meta http-equiv="x-ua-compatible" content="IE=edge">
`&#64;endpush`

`&#64;push`('scripts')
   &lt;script>
      $(document).ready(function(){
         console.log('Loaded!')
      })
   &lt;/script>
`&#64;endpush`</code></pre>


<p>In addition to that, if you have followed the `laravel-mix` default naming convention for assets, the template will also <b>automatically include the generated CSS and JS files</b> (assuming the files exist). The default naming convention is the following:</p>

<ul>
  <li>`css/app.css`</li>
  <li>`js/manifest.js`</li>
  <li>`js/vendor.js`</li>
  <li>`js/app.js`</li>
</ul>

<p>However, if you chose a different naming conventions or need to include multiple separate assets, you may pass an array of CSS and JS paths in the `VlStyles` and `VlScripts` variables:</p>

<pre><code class="language-php">&#64;extends('vuravel-menu::app', [
   'VlStyles' => [
      mix('css/main-styles.css'),
      asset('css/custom-styles.css')
   ],
   'VlScripts' => [
      mix('js/custom-manifest.js'),
      asset('js/vendor/custom-vendor.js'),
      mix('js/main-script.js')
   ]
])</code></pre>

<h3>Favicons</h3>

<p>
  Vuravel currently offers a free template for favicons generated with <a href="https://realfavicongenerator.net" target="_blank">realfavicongenerator.net</a>. All you have to do is to dump the favicons generated by this site into your `public/favicon` folder and Vuravel will pick it up.
</p>

<p>If you wish to use any other favicon assets, just push them in the `header` section.</p>

<pre><code class="language-html">`&#64;push`('header')
&lt;link rel="icon" type="image/png" href="favicon-32x32.png">
&lt;meta name="msapplication-TileColor" content="#da532c">
&lt;meta name="theme-color" content="#ffffff">
`&#64;endpush`</code></pre>

<h3>SEO Notice</h3>

<p>Unlike with other `vuravel` modules, it is important for navbars, sidebars and menus in general to be rendered on the server side for improved SEO. For that reason, the menu items have been built using <b>Blade</b> and not Vue.js.</p>

<!-- ------------------------------------------------------------- -->
<div class="wave"></div>
<!-- ------------------------------------------------------------- -->

<h2>Writing Menus</h2>

<!-- ------------------------------------------------------------- -->
<h3>Artisan command</h3>

<div class="flex flex-col items-center sm:flex-row sm:justify-between sm:items-start sm:mb-6">
<div class="w-full sm:flex-1 sm:mr-4">
<p>
    From the root directory of your project, you can generate a Menu template using the following command with the class name as a parameter. This will create the template class in the `app/Menus` directory of your application. 
</p>

<pre><code class="language-none">php artisan vuravel:menu MyMenu</code></pre>

<p>
    You may also target a subfolder. For example:
</p>

<pre><code class="language-none">php artisan vuravel:menu Dashboard/AdminSidebar</code></pre>

@tip(While not technically required, it is recommended that you follow this folder structure convention.)

</div>
<img src="{{asset('img/menu-folder.png')}}" alt="Menu folder structure">
</div>


<!-- ------------------------------------------------------------- -->
<h3>Menu template</h3>

<p>
    The bare minimum for a <b>Menu</b> class is to have a `components()` method where its' different menu items are declared. You may also
</p>

<pre><code class="language-php">&lt;?php

namespace App\Menus;

class MyMenu extends \VlMenu
{
   public $placement = 'fixed'; //TODO explanation
   public $top = true;
   public $containerClass;

   public $class = '';
   public $id = '';


   //This is the only required method. It returns an array of the menu's components.
   public function components() { ... }

   //This method is fired at the very beginning of the cycle, before any other method.
   public function created() { ... }
}
</code></pre>

<p>
  If you do not wish to use the composer alias `\VlMenu`, you may also import the Form class by adding the full namespace in a use statement.
</p>

<pre><code class="language-php">use Vuravel\Components\Menu; //<-- Full namespace of Vuravel's Menu class

class MyMenu extends Menu
{
  ...
</code></pre>

<!-- ------------------------------------------------------------- -->
<div class="wave"></div>
<!-- ------------------------------------------------------------- -->

    <h2>Turbo links</h2>


<!-- ------------------------------------------------------------- -->
<div class="wave"></div>
<!-- ------------------------------------------------------------- -->

    <h2>Menu components</h2>

<p>
    Vuravel offers an array of menu-specific components such as <b>Dropdown</b>, <b>CollapseOnMobile</b>, <b>Logo</b>... but you may also include any other components from the library such as <b>Html</b>, <b>Link</b>, <b>Button</b> or even form fields...
</p>

<pre><code class="language-php">public function components()
{ 
   return [
      Logo::form('&lt;b>Vuravel&lt;/b>')->image('img/vuravel-logo.png'),
      NavSearch::form('Search the docs...'),
      CollapseOnMobile::form('&#9776;')->leftMenu(

         Dropdown::form('Docs')->submenu(
            Link::form('vuravel/catalog')->href('docs.catalog'),
            Link::form('vuravel/form')->href('docs.form'),
            Link::form('vuravel/menu')->href('docs.menu')
         ),
         Button::form('Contact us')->post(...)->inModal()

      )->rightMenu(

         \Auth::user() ?
            VlAuthMenu::form(\Auth::user()->name)->icon('fa fa-user') :
            VlLink::form( __('Login') )->loadsView('login.modal')->inModal()

      )
   ];
}</code></pre>

  <h3>Logo</h3>
  @include('api.component-desc-doc',['component' => 'Logo'])
  <h3>Collapse</h3>
  @include('api.component-desc-doc',['component' => 'Collapse'])
  <h3>CollapseOnMobile</h3>
  @include('api.component-desc-doc',['component' => 'CollapseOnMobile'])
  <h3>Dropdown</h3>
  @include('api.component-desc-doc',['component' => 'Dropdown'])
  <h3>NavSearch</h3>
  @include('api.component-desc-doc',['component' => 'NavSearch'])

	<h3>Navbar template</h3>

	<p>An extensive example of a Navbar can be seen below. </p>

<pre><code class="language-php">namespace App\Menus;

class Navbar extends \VlMenu
{
  public $placement = 'fixed';

  public function components()
  {
     return [
        VlLogo::form('&lt;b>Vuravel&lt;/b>')->image('img/vuravel-logo.png'),
        VlNavSearch::form('Search the docs...'),
        VlCollapseOnMobile::leftMenu(

           VlDropdown::form('Docs')->submenu(
              VlLink::form('vuravel/form')->href('form-docs'),
              VlLink::form('vuravel/menu')->href('menu-docs')
           ),
           VlLink::form('Support Vuravel')->href('support'),
           VlLocales::form(session('locale'))

        )->rightMenu(

           \Auth::user() ?
              VlAuthMenu::form(\Auth::user()->name)->icon('fa fa-user') :
              VlLink::form( __('Login') )->loadsView('login.modal')->inModal()

        )
     ];
  }
}</code></pre>

  <h3>Writing Sidebars</h3>


<h2>Direct Route call</h2>

@include('docs.1-0.direct-route')




	<h3>{{ __("Using a custom layout") }}</h3>

	<p>To display the menu in your view, you can leverage the vuravel `menu()` helper function to display the menu's components in your blade file:</p>

<pre><code class="php">`@<span></span>include`('vuravel-menu::menus.nav', [ 'Navbar' => `menu`('Navbar') ])</code></pre>

	<p>Note that `menu()` will first look in the `app\Menus` directory of your application and look for the class named as it's argument. The argument can also be a path to a subfolder, for example: ` menu('SubFolder\MyCustomMenu') `.</p>

</div>

@endsection
