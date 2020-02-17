@extends('docs.layout')

@section('doc-title', 'Vuravel catalogs docs')
@section('seo-title', 'Tables & Catalogs 🤔 Automated browsing, filtering, sorting & pagination')

@section('doc-content')

@tip(If you wish to have some unavailable functionality, please let us know through one of our feedback channels and we promise to look into it as soon as we can.)

<h2>Writing a catalog</h2>

<!-- ------------------------------------------------------------- -->
<h3>Artisan command</h3>

<div class="flex flex-col items-center sm:flex-row sm:justify-between sm:items-start sm:mb-6">
<div class="w-full sm:flex-1 sm:mr-4">
<p>
    From the root directory of your project, you can generate a form template using the following command with the class name as a parameter. This will create the template class in the `app/Catalogs` directory of your application. 
</p>

<pre><code class="language-none">php artisan vuravel:catalog MyCatalog</code></pre>

<p>
    You may also target a subfolder. For example:
</p>

<pre><code class="language-none">php artisan vuravel:catalog Forum/QuestionsCatalog</code></pre>

@tip(The folder structure convention is not technically required, it is just the default one vuravel uses. You may organize your vuravel forms and catalogs by concerns too.)

</div>
<img src="{{asset('img/catalog-folder.png')}}" alt="Form folder structure">
</div>


<!-- ------------------------------------------------------------- -->
<h3>Catalog template</h3>

<p>
    The Catalog class has three important sections.
</p>

<ul>
  <li>The <b>query</b> or model assignment.</li>
  <li>The <b>card($item)</b> method where the different variables that a card needs are declared.</li>
  <li>The <b>filters</b> and other options.</li>
</ul>

<pre><code class="language-php">&lt;?php

namespace App\Catalogs;

class MyCatalog extends \VlCatalog
{
   /******* The properties **********/
   public $layout = 'Masonry';
   ...

   /******* The query section *******/
   public function query() { ... }

   /******* The card section ********/
   public function card($item) { ... }

   /******* The filters section *****/
   public function top() { ... }
   public function right() { ... }
   public function bottom() { ... }
   public function left() { ... }
}
</code></pre>

<p>
  If you do not wish to use composer the alias `\VlCatalog`, you may also import the Catalog class by adding the full namespace in a use statement.
</p>

<pre><code class="language-php">use Vuravel\Components\Catalog; //<-- Full namespace of Vuravel's Catalog class

class MyCatalog extends Catalog
{
  ...
</code></pre>


<!-- ------------------------------------------------------------- -->
<h3>Declaring a Catalog</h3>

  <p>When no additional parameters are needed to display the catalog, you may simply declare the class with the new keyword as you would any class in PHP.</p>

<pre><code class="language-php">$myCatalog = new `MyCatalog`();</code></pre>

<!-- ------------------------------------------------------------- -->
<h4>Catalog parameters</h4>

<p>
    If you need to inject one or more external parameters or dependencies, you may use the `store` method which allows you to <b>store an associative array of key/value variables in the "session" of the catalog</b>.
</p>

<p>
    For example, let's say you want to display the answers to a question in a form, you may include the Question's id in the catalog's store:
</p>

<pre><code class="language-php">class QuestionForm extends \VlForm
{
    public function components()
    {
      return [
         Input::form('Title'),
         Textarea::form('Content'),
         //We pass the Question's id here
         AnswersCatalog::store(['question_id' => $this->recordKey])
      ];
    }
</code></pre>

<p>
    Then in your <b>Catalog</b> class, you can retrieve the question id thanks to the `store()` method. Since this data is stored in the session, it is accessible at all stages of the catalog's lifecycle (i.e. on initial load AND all subsequent browsing, filtering or sorting calls).
</p>

<pre><code class="language-php">class AnswersCatalog extends \VlCatalog
{
    public function query()
    {
       //We retrieve the question_id here
       return Answer::where('question_id', $this->store('question_id'));
    }

...</code></pre>

<!-- ------------------------------------------------------------- -->
<div class="wave"></div>
<!-- ------------------------------------------------------------- -->

    <h2>Catalog properties</h2>

<p>
    Here we define the Catalog's high-level properties or settings like the layout or pagination options. There are also other modifiable settings that are covered in the sorting, ordering and filtering sections.
</p>

<!-- ------------------------------------------------------------- -->
<h3>The layout section</h3>

<p>
    Currently, there are 4 Catalog layouts available:
</p>

<ul>
  <li>Table: display the items in table rows.</li>
  <li>Horizontal (the default layout): cards are just stacked in rows.</li>
  <li>Grid: this leverages Bootstrap's infamous grid system.</li>
  <li>Masonry: to display different height cards beautifully and responsively.</li>
</ul>

<p>
   To set the layout, you declare the public property `$layout` at the beginning of your Catalog class:
</p>

<pre><code class="language-php">class MyCatalog extends Catalog
{
   public $layout = 'Horizontal'; //The layout style where cards will be displayed.

   ...
</code></pre>

<!-- ------------------------------------------------------------- -->
<h3>Pagination</h3>

<p>
   All catalogs are paginated out of the box (see the query section for more info). You also define the pagination settings as properties.
</p>

<pre><code class="language-php">
class MyCatalog extends Catalog
{
   public $perPage = 50; //The amount of items per page.
   public $noItemsFound = 'No items found'; //The message to display when no items are found.

   public $hasPagination = true; //Whether to display pagination links or not
   public $topPagination = true; //Whether to display pagination links above the cards
   public $bottomPagination = false; //Whether to display pagination links below the cards
   public $leftPagination = false; //Whether to align pagination links to the left or to the right

   public $paginationStyle = 'Links'; //The pagination component. Other option is 'Showing'

   ...
</code></pre>

<p>
  You may chose between different pagination styles. Currently, the choices are the following but more will be added to the library:
</p>

<?php 
$pagination = json_encode([
  'current_page'=> 1,
  'data'=> [],
  'from'=> 1,
  'last_page'=> 4,
  'per_page'=> 6,
  'prev_page_url'=> null,
  'to'=> 6,
  'total'=> 21
]);
?>

<div class="row">
  <div class="col-6">
    <p>`Links`</p>
    <vl-pagination-links :pagination="{{ $pagination }}"></vl-pagination-links>
  </div>
  <div class="col-6">
    <p>`Showing`</p>
    <vl-pagination-showing :pagination="{{ $pagination }}"></vl-pagination-showing>
  </div>
</div>

<!-- ------------------------------------------------------------- -->
<div class="wave"></div>
<!-- ------------------------------------------------------------- -->

<!-- ------------------------------------------------------------- -->
<h2>The query section</h2>

<p>
   This is where you specify the query that will get the items that will be displayed on first load.
</p>

<pre><code class="language-php">
class MyCatalog extends Catalog
{
   ...

   public function query()
   {
      return Post::with('tags')->orderBy('published_at');
   }

   ...
</code></pre>

<h3>The return value of query</h3>

@danger(Notice how we return a <b>Builder</b> instance and <u>NOT a Collection</u>, i.e. there is no ->get() method at the end of the return value.)

<p>
    <i class="far fa-thumbs-up color3"></i> It is recommended that a <b>query</b> method return:
</p>

<ul>
  <li>either an `Illuminate\Database\Eloquent\Builder` instance,</li>
  <li>or an `Illuminate\Database\Query\Builder` instance.</li>
</ul>

<p>
   While it is possible to also return:
</p>
<ul>
  <li>an `Illuminate\Database\Eloquent\Collection` instance,</li>
  <li>an `Illuminate\Support\Collection` instance,</li>
  <li>or even a simple `Array`</li>
</ul>

<p>it comes at a performance cost and poorer filtering capabilities. Use it only if these consequences are not important for the Catalog in question.</p>

<h4>Eloquent ORM methods</h4>

<p>
     You may use <b>any Eloquent ORM method</b> such as:
</p>

<ul>
  <li>where, whereHas, whereIn, having, ... to prefilter the records.</li>
  <li>with, withCount, ... to eager load relationships.</li>
  <li>orderBy, orderByRaw, ... to order your records.</li>
  <li>groupBy, skip, take, ... to group or limiting your records.</li>
</ul>

<pre><code class="language-php">public function query()
{
  return Post::whereHas(...)->withCount(...)->orderByRaw(...);
}</code></pre>

<p>
  Or to query all the records, you may simply write:
</p>

<pre><code class="language-php">public function query()
{
  return new Post(); //<-- This will paginate through ALL the posts in the DB
}</code></pre>

<p>
  For filtering items after initial load, You may then filter the records but that will be handled out of the box by the filters in the filter section.
</p>

<!-- ------------------------------------------------------------- -->
<div class="wave"></div>
<!-- ------------------------------------------------------------- -->

<!-- ------------------------------------------------------------- -->
<h2>Catalog Cards</h2>

<p>
   The information needed to display each card is given in the `card` method. This method is always declared with a single parameter `$item` which comes from the paginated query results. There are 3 ways of calling the card method depending on the level of customization you want to have.
</p>

<!-- ------------------------------------------------------------- -->
<h3>Predefined Cards</h3>

<p>
   Vuravel offers some predefined card components that you may just reference and fill the relevant info in the corresponding card properties. For example, here we are using the `ImageTitleOverlay` card and adding the article's image, title, grid and styles class and finally a toolbar of buttons to like or share the article.
</p>

<pre><code class="language-php">public function card($item)
{
   return ImageTitleOverlay::form([
     'image' => asset($item->image),
     'title' => $item->title,
     'col' => 'col-4',
     'class' => 'shadow mb-6',
     'buttons' => FlexEnd::form(
        Link::icon('icon-heart')->post('article.like', ['id' => $item->id]),
        Link::icon('icon-share')->post('article.share', ['id' => $item->id])
     )
   ]);
}</code></pre>

@tip(To see all the predefined components and their required properties, check out the <a href="{{ route('card-api', ['card' => 'TableRow', 'layout' => 'Table']) }}" target="_blank">API for Catalog components</a>.)

<!-- ------------------------------------------------------------- -->
<h3>Custom PHP card</h3>

<p>
   You may build a card in PHP relatively quickly using vuravel's components and adding the required classes and functionality by chaining some methods as you would do in Forms:
</p>

<pre><code class="language-php">public function card($item)
{
   return Columns::form(
      Html::form('&lt;img src="'.asset($item->image).'">')
         ->col('col-3'),
      Rows::form(
         Title::form($item->title),
         Html::form('&lt;p>'.$item->description.'&lt;/p>'),
         FlexEnd::form(
            Link::icon('icon-heart')->post('article.like', ['id' => $item->id]),
            Link::icon('icon-share')->post('article.share', ['id' => $item->id])
         )
      )->col('col-9')
   );
}</code></pre>

<!-- ------------------------------------------------------------- -->
<h3>Custom Vue card</h3>

<p>
  For even more power and flexibility, you may also create your own Vue card component. Let's create a custom card example in our `VlSection.vue` file. To retrieve the data passed from the back-end, you may use the `$_prop` function that you get from the imported Card mixin:
</p>

<pre><code class="language-html">&lt;template>
    &lt;div class="row">
        &lt;div class="col-sm-5">
            &lt;h3 :id="`$_prop`('slug')" v-html="`$_prop`('title')"/>
            &lt;p v-html="`$_prop`('description')" />

            &lt;div v-for="code in `$_prop`('codes')">
                &lt;vl-form v-if="code.preview" :vcomponent="code.preview" />
            &lt;/div>

        &lt;/div>
        &lt;div class="col-sm-7">
            &lt;vl-tabs class="code-tabs">
                &lt;vl-tab v-for="code in `$_prop`('codes')" :key="code.id" :name="code.title">

                    &lt;pre class="mt-0">&lt;code class="language-php" v-html="code.phpCode"/>&lt;/pre>

                &lt;/vl-tab>
            &lt;/vl-tabs>
        &lt;/div>
    &lt;/div>
&lt;/template>

&lt;script>
import Card from 'vuravel-catalog/js/mixins/Card'
export default {
    mixins: [Card] //!! IMPORTANT !! - Import and use the Card mixin 
}
&lt;/script>
</code></pre>

<p>
  Then we need to feed it the relevant information from the back-end. This works exactly as you would with predefined components except we call the general `Card` class and modify the front-end component it uses with the `component` method:
</p>

<pre><code class="language-php">use Vuravel\Components\Card;

class CustomCatalog extends \VlCatalog
{
    ...

    public function card($item)
    {
       return Card::form([
          'title' => $item->title,
          'slug' => Str::slug($item->title),
          'description' => $item->description,
          'codes' => $item->codes->each(function($code){
              $code->phpCode = $code->phpCode();
              $code->preview = $code->form ? $code->form->preview() : null;
          })
       ])->component('Section'); //notice how we tell which Front-end component to use
    }</code></pre>

<!-- ------------------------------------------------------------- -->
<div class="wave"></div>
<!-- ------------------------------------------------------------- -->

<!-- ------------------------------------------------------------- -->
<h2>Filters & Components</h2>

<!-- ------------------------------------------------------------- -->
<h3>Filters placement</h3>

<p>
   You may include additional html, filters, and other components all around your catalog's cards. There are 4 methods `top`, `right`, `left` and `bottom` that can be used for positionning these components and they have to return a series of components the same way as you would in a Form or Card:
</p>

<div class="mansala color2 font-bold text-sm p-2 border border-gray-200 mb-6">
  <div class="flex justify-between items-stretch h-64">
    <div class="bg-gray-200 vlFlexCenter vlFlexCol p-2 text-center mr-4">
      <span class="hidden md:inline">Components<br>in </span> left()
    </div>
    <div class="flex-1">
      <div class="h-10 bg-gray-200 vlFlexCenter p-2 text-center mb-4">
        <div><span class="hidden md:inline">Components in </span> top()</div>
      </div>
      <div class="bg-gray-200">
        <div class="h-8 text-gray-600 vlFlexCenter">Layout</div>
        <div class="row">
          <div class="col-4 mb-2"><div class="bg-gray-400 vlFlexCenter h-12">Card</div></div>
          <div class="col-4 mb-2"><div class="bg-gray-400 vlFlexCenter h-12">Card</div></div>
          <div class="col-4 mb-2"><div class="bg-gray-400 vlFlexCenter h-12">Card</div></div>
          <div class="col-4 mb-2"><div class="bg-gray-400 vlFlexCenter h-12">Card</div></div>
          <div class="col-4 mb-2"><div class="bg-gray-400 vlFlexCenter h-12">Card</div></div>
          <div class="col-4 mb-2"><div class="bg-gray-400 vlFlexCenter h-12">Card</div></div>
        </div>
      </div>
      <div class="h-10 bg-gray-200 vlFlexCenter p-2 text-center mt-4">
        <div><span class="hidden md:inline">Components in </span> bottom()</div>
      </div>
    </div>
    <div class="bg-gray-200 vlFlexCenter vlFlexCol p-2 text-center ml-4">
      <span class="hidden md:inline">Components<br>in </span> right()
    </div>
  </div>
</div>

<p>
  In this example, we enrich our catalog by adding on top of some components that either explain or interact with our catalog.
</p>

<pre><code class="language-php">public function top()
{
  return [
     //Adding a title and a link to add a new item
     FlexBetween::form(
        Title::form('Examples'),
        Link::form('Add an example')->href('admin.example')
     ),
     //Adding filters (see next section)
     Columns::form(
        Select::form('Category')->optionsFrom('id','name')
            ->filtersCatalog(),
        Input::form('Title')
            ->filtersOnInput(500)
     )
  ];
}</code></pre>


<!-- ------------------------------------------------------------- -->
<h3>Filtering</h3>

<p>
   It is very straightforward to filter your catalog's cards. To do so, you may add one or more <b>Field component</b> in one of the filters sections and then chain one of the filtering actions.
</p>

@tip(Only Field components can filter catalogs. If you wish to filter using a Button or Link, you may use the <b>SelectButtons</b> or <b>SelectLinks</b> components which are Fields.)

<pre><code class="language-php">public function top()
{
   return [
      Select::form('Category')
         ->filtersCatalog('category.name'),
      Input::form('Title')
         ->filtersOnInput(500, 'title')
   ];
}</code></pre>

<h4>Filtering actions</h4>

<p>
   The following actions may be used to filter:
</p>

<table class="api-table table table-sm table-borderless">
  <tbody>
{!! apiMethod('Vuravel\\Form\\Field', 'filtersCatalog') !!}
{!! apiMethod('Vuravel\\Form\\Field', 'filtersOnInput') !!}
  </tbody>
</table>

<p>
   Note that the `$filterKey` parameter can be:
</p>

<ul>
  <li>Empty `null` : in this case, the snake-cased version of the label is used to filter.</li>
  <li>A simple string `'attribute'` : to refer to one of the model's attributes.</li>
  <li>A dot-separated string `'relationship.attribute'` or `'relationship.relationship.attribute'` : to filter by nested relationships. Vuravel currently supports two levels deep nesting.</li>
</ul>

<h4>Filtering conditions</h4>

<p>
    The default conditions for filtering used by Vuravel are the following:
</p>

<ul>
  <li>If the Field can support multiple values (MultiSelect for example), it will use a whereIn.</li>
  <li>If the Field is an Input, it will use a where($column, 'LIKE', '%'.$value.'%').</li>
  <li>Otherwise, it will perform a simple where.</li>
</ul> 

<p>
   If you have multiple filters, Vuravel will perform an AND where.
</p>

<h4>Prefilter a Catalog</h4>

<p>
  Of course, if you want to prefilter your Catalog, you may define that in the `query` method. This filter is permanent and will always be preserved on subsequent browse requests.
</p>

<pre><code class="language-php">public function query()
{
   //Permanent Catalog filter
   return Article::whereNotNull('published_at');
}</code></pre>

<!-- ------------------------------------------------------------- -->
<h3>Sorting</h3>

<p>You may also define sorting capabilities in your Catalog as easily as filters. The components that are capable of doing sorting are: </p>

<ul>
  <li>Fields: in this case their values will determine the sort order.</li>
  <li>Buttons and Links: you have to instruct the sort order with one of the sorting actions.</li>
  <li>Th (table headers): you also have to instruct the sort column and direction in one of the sorting actions.</li>
</ul>

<h4>Sorting action</h4>

<p>
   The following action may be used to sort:
</p>
<table class="api-table table table-sm table-borderless">
  <tbody>
{!! apiMethod('Vuravel\\Form\\Field', 'sortsCatalog') !!}
  </tbody>
</table>

<p>
   The `$sortOrders` parameter accepts a pipe-delimited string of one or more `column:direction` pairs. You may also sort on nested relationships (one level deep only) by using a dot-delimited string in the column part:
</p>

<ul>
  <li>Sorting by an attribute `'attribute'` : this will sort by the model's attribute and the direction is ASC by default.</li>
  <li>Sorting by one attribute with a direction `'attribute:DESC'`: this will sort by the model's attribute in the descending order.</li>
  <li>Sorting by multiple attributes and directions `'attribute1:DESC|attribute2|attribute3:DESC'` : this will sort by 3 different attributes with attribute1 and attribute3 in the descending order and attribute2 in the ascending order.</li>
  <li>Sorting with relationships and attributes `'relationship.attribute1:DESC|attribute2'` : this will sort by the model's attribute2 in the ascending order and the relationship's attribute1 in the descending order.</li>
</ul>

<h4>Presort a Catalog</h4>

<p>
  Of course, if you want a presorted Catalog, you may define that in the `query` method. This sort will always be preserved on subsequent browse requests.
</p>

<pre><code class="language-php">public function query()
{
   //Permanent Catalog sort
   return Article::orderBy('published_at', 'DESC');
}</code></pre>

<!-- ------------------------------------------------------------- -->
<h3>Draggable ordering</h3>

<p>
  Vuravel also offers the ability to drag and drop cards and reorder the records on the back-end. Let's say your model has an `order_column` column that will be used to display the items in the desired order.
</p> 
<p>
  You may set the `$orderable` property to activate this functionality:
</p>

<pre><code class="language-php">class MyCatalog extends \VlCatalog
{
   public $orderable = 'order_column';</code></pre>

<p>
  Now the Catalog cards will be draggable and the user may change the cards' orders from the Front-end and the Back-end will get automatically updated.
</p>

@tip(The orderable column should be defined as an INT datatype or equivalent in your database.)


<!-- ------------------------------------------------------------- -->
<div class="wave"></div>
<!-- ------------------------------------------------------------- -->

    <h2>Displaying the catalog</h2>


<!-- ------------------------------------------------------------- -->
<h3>The Vue component</h3>

<p>
    The front-end component `vl-catalog` has one required prop `:vcomponent` prop where you inject the instantiated PHP Catalog class. Vuravel encodes it automatically to JSON.
</p>

<pre><code class="language-html" v-pre>&lt;vl-catalog :vcomponent="&#123;{ new App\Catalogs\MyCatalog() }}">&lt;/vl-catalog></code></pre>

@tip(Remember to place the `&lt;vl-catalog>` inside the bootable Vue.js element, which is usually the div with id `#app`.)

<!-- ------------------------------------------------------------- -->
<h3>Render in Blade</h3>

<p>
    The `render` method will directly generate the vue component for you. This is nothing more than syntactic sugar for the previous step.
</p>

<pre><code class="language-html" v-pre>&lt;!-- In your blade template -->
&#123;!! App\Catalogs\MyCatalog::render() !!}

&lt;!-- Same thing as this -->
&lt;vl-catalog :vcomponent="&#123;{ new App\Catalogs\MyCatalog() }}">&lt;/vl-catalog></code></pre>


<!-- ------------------------------------------------------------- -->
<h3>Usage in Vue</h3>

<p>
    You may want to display the catalog inside one of your Vue components. In this case, just pass the catalog as a prop and use it in Vue.
</p>

<pre><code class="language-html" v-pre>&lt;!-- In your blade template -->
&lt;my-vue-component :catalog="&#123;{ new App\Catalogs\MyCatalog() }}">&lt;/my-vue-component></code></pre>

<pre><code class="language-html" v-pre>&lt;!-- MyVueComponent.vue -->
&lt;template>
   &lt;vl-catalog :vcomponent="catalog" @event="handleEvent">&lt;/vl-catalog>
&lt;/template>

&lt;script>
export default {
   props: ['catalog'],
   methods: {
     handleEvent(payLoad){
       `console`.log(payLoad)
     }
   }
}
&lt;/script>
</code></pre>


<!-- ------------------------------------------------------------- -->
<h3>Direct Route call</h3>

@include('docs.1-0.direct-route')

@endsection