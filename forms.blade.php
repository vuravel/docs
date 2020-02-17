@extends('docs.layout')

@section('doc-title', 'Vuravel forms docs')
@section('seo-title', 'Single-class Self-handling Eloquent-ready Full-stack Forms 👌')

@section('doc-content')

<h2>Writing a form</h2>

<!-- ------------------------------------------------------------- -->
<h3>Artisan command</h3>

<div class="flex flex-col items-center sm:flex-row sm:justify-between sm:items-start sm:mb-6">
<div class="w-full sm:flex-1 sm:mr-4">
<p>
    From the root directory of your project, you can generate a form template using the following command with the class name as a parameter. This will create the template class in the `app/Forms` directory of your application. 
</p>

<pre><code class="language-none">php artisan vuravel:form MyForm</code></pre>

<p>
    You may also target a subfolder. For example:
</p>

<pre><code class="language-none">php artisan vuravel:form Forum/AnswerForm</code></pre>

@tip(The folder structure convention is not technically required, it is just the default one vuravel uses. You may organize your vuravel forms and catalogs by concerns too.)

</div>
<img src="{{asset('img/form-folder.png')}}" alt="Form folder structure">
</div>


<!-- ------------------------------------------------------------- -->
<h3>Form template</h3>

<p>
    The bare minimum for a <b>Form</b> class is to have a `components()` method where its' different elements are declared. The rest of the methods are optional - but very useful.
</p>

<pre><code class="language-php">&lt;?php

namespace App\Forms;

class MyForm extends \VlForm
{
   //This is the only required method. It returns an array of the form's components.
   public function components() { ... }

   //This method is fired at the very beginning of the cycle, before any other method.
   public function created() { ... }

   //Handles validation. See validation section for more info.
   public function rules() { ... }

   //Handles submit authorization. See authorization section for more info.
   public function authorize() { ... }

   //Handles a complex submit route declaration. See submission section for more info.
   public function submitUrl() { ... }

   //If you wish to handle the submit functionnality yourself. See submission section for more info.
   public function handle($request) { ... }

   //Eloquent lifecyclehooks. See Eloquent & Responses section for more info.
   public function beforeSave($model) { ... }
   public function afterSave($model) { ... }
   public function response() { ... }
   public function completed($model) { ... }
}
</code></pre>

<p>
  If you do not wish to use the composer alias `\VlForm`, you may also import the Form class by adding the full namespace in a use statement.
</p>

<pre><code class="language-php">use Vuravel\Components\Form; //<-- Full namespace of Vuravel's Form class

class MyForm extends Form
{
  ...
</code></pre>

<!-- ------------------------------------------------------------- -->
<h3>Declaring a Form</h3>

  <p>When no additional parameters are needed to display the form, you may simply declare the class with the new keyword as you would any class in PHP.</p>

<pre><code class="language-php">$myForm = new `MyForm`();</code></pre>

@tip(There are other ways to declare a Form, namely using:<br>- <b>find</b> to update an Eloquent Model. See "Eloquent Form" section for more info, or<br>- <b>store</b> to inject parameters or dependencies. See "Session store" section for more info.)

<pre><code class="language-php">$myForm = MyForm::find($id);
$myForm = MyForm::store(['parent_id' => $parentId]);</code></pre>

<!-- ------------------------------------------------------------- -->
<div class="wave"></div>
<!-- ------------------------------------------------------------- -->

    <h2>Form components</h2>

<p>
    There are 4 categories of components: 
</p>

<ul>
  <li><a href="#field-components">Field components</a></li>
  <li><a href="#layout-components">Layout components</a></li>
  <li><a href="#other-components">Triggers and Blocks (Other components)</a></li>
</ul>

<!-- ------------------------------------------------------------- -->
<h3>Instatiating Components</h3>

<p>
    There are 3 ways to instantiate the components depending on the developer's preference. 
</p>

<ul>
    <li>
        Static method `::form` with full namespace.<br>
        <small>Safest (no name conflicts, full IDE utilities, ...)</small>
    </li>
    <li>
        Using prefixed Composer aliases.<br>
        <small>Safe (no name conflicts, loss of IDE Goto Definition)</small>
    </li>
    <li>
        Prefixed helper functions: 'Vl' + Component name. <br>
        <small>Can cause naming conflicts. Use only if you are certain no Vl+Component function exists.</small>
    </li>
</ul>

<h4>Static method `::form` with namespace</h4>

<p>
    The best and safest way (no naming conflicts since you are calling the specific class directly) is to <b>instantiate</b> by calling the static function `::form`. This allows us to then chain additional methods.
</p>

<pre><code class="language-php">//Namespaces always start with Vuravel\Components\...
use Vuravel\Components\Input;
use Vuravel\Components\Button;
use Vuravel\Components\Columns;

//or shorter:
use Vuravel\Components\{Input, Button, Columns};

...

//Non-layout components: the first parameter is always the label.
Input::form('Enter your phone number')
Button::form('&lt;span>Save&lt;/span>')  //<--You may pass it HTML too.

//Layout components: you may add as many component arguments.
Columns::form(
  Input::form('First Name'),
  Input::form('Last Name'),
  ...
)
</code></pre>

<h4>Prefixed Composer aliases</h4>

<p>
    Another shorter way that rids us of the need of writing namespaces at the top of our classes and has no risk of causing naming conflicts. The syntax is \Vl + Component name:
</p>

<pre><code class="language-php">//No need to import namespaces.
\VlInput::form('Enter your phone number')
\VlButton::form('&lt;span>Save&lt;/span>')
\VlColumns::form(
   \VlInput::form('First Name'),
   \VlInput::form('Last Name')
)
</code></pre>

<h4>Prefixed helper functions</h4>

<p>
    Finally, if you are certain that your project has no Vl + Component name function, you may use the following helper functions that have the advantage of ridding us of the need of namespacing AND the static `::form` method:
</p>

<pre><code class="language-php">//No need to use namespace or static function call
VlInput('Enter your phone number')
VlButton('&lt;span>Save&lt;/span>')
VlColumns(
   VlInput('First Name'),
   VlInput('Last Name')
)
</code></pre>

<p>
  Great! Now, let's dive into more details on component arguments and methods chaining.
</p>

<!-- ------------------------------------------------------------- -->
<h3>Field Components</h3>

<p>
  <b>Field Components</b> represent the user's input in the form. They send values to the back-end and handle errors.
</p>

  <h4>Field label & name attribute</h4>

  <p>Typically, field components are "formed" using the static method `::form($label)` to which you would pass a human readable label. This will do two things: first, assign a default label using the double underscore `__` helper function in Laravel; secondly, set the name attribute of the field to the `snake_case($label)`.
   For example:</p>

<pre><code class="language-php">// This will have a label of __('First Name')
// and a name attribute of 'first_name'
Input::form('First Name')
</code></pre>

  <p>If however, the desired name attribute doesn't correspond to the snake cased version of the label, you may explicitely set it by chaining the `name()` method to the component:</p>

<pre><code class="language-php">Input::form('Enter your phone number')->name('phone_number')</code></pre>

    <h4>Assigning a value</h4>

  <p>To set a certain value to a field, for example, when dealing with hidden fields, you may chain the `->value($value)` method to the component:</p>

<pre><code class="language-php">Hidden::form('source')->value('vuravel')</code></pre>

  <p>There are many other useful methods for fields including toggling and backend requests. To see the full list, check out the <a href="{{route('component-api', ['component' => 'Input'])}}" alt="Form components API" target="_blank">form components api</a>.</p>

  <h3>Layout Components</h3>

  <p>`Layout Components` allow you to organize your form's components in complex layouts, tabs or steps. They can have as many arguments as you like. A `Columns`component for example will accept as many arguments as desired in the chosen layout:</p>

<pre><code class="language-php">`Columns`::form(
   `Input`::form('First name'),
   `Input`::form('Last name'),
   `Date`::form('Birthday')
   //... or more
)
</code></pre>

<!-- ------------------------------------------------------------- -->
<h3>Other Components</h3>

<h4>Trigger Components</h4>

<p>
    `Trigger Components` allow users to interact with the form and perform AJAX requests. You pass them a the label as parameter, that renders html content. Note that here the label must be translated a priori.
</p>

<pre><code class="language-php">Button::form('Start timer')
   ->submitsForm() // <-- The button now will submit the form

Link::form(__('Login'))  //<-- a priori translation
   ->url('login')</code></pre>

  <h4>Block Components</h4>

<p>
    `Block Components` do nothing but display HTML but you will use them a lot nonetheless. They allow adding HTML elements to your form such as a Title, Alerts or Tips.
</p>

<pre><code class="language-php">Title::form('Edit your post')

Html::form('Please make sure your name is exactly as in your passport')
   ->`class`('alert alert-danger')</code></pre>

<!-- ------------------------------------------------------------- -->
<h3>Methods Chaining API</h3>

<p>
    You may then chain a series of methods to enrich the field's properties and features. For example:
</p>

<pre><code class="language-php">//Setting the name attribute and making the field required
Input::form('Your full name')
  ->name('full_name')
  ->required()

//Setting a default country
Country::form('Pick a country')
   ->name('country')
   ->`default`('CA')

//Image upload with automatic thumbnail creation.
Image::form('Profile pic')
   ->withThumbnail()
   ->extraAttributes(['collection' => 'cover-photo'])

//Columns example with different grid widths and top alignment.
Columns::form(

   Date::form('Contact date')->col('col-4'),
   Textarea::form('Subject')->col('col-8')

)->alignStart()</code></pre>

<p>
    Each component has it's own set of well-documented methods. They can be specific to the component, to it's category (field, layout, ...) or shared by all of them.
</p>

<a href="{{route('component-api', ['component' => 'Input'])}}" class="vlBtn vlBtnSecondary">Check out all the available methods</a>

<!-- ------------------------------------------------------------- -->
<div class="wave"></div>
<!-- ------------------------------------------------------------- -->

    <h2>Eloquent Form</h2>

<!-- ------------------------------------------------------------- -->
<h3>Assign an Eloquent model</h3>

<p>
    If you want to take advantage of automated methods to create or update a corresponding Eloquent model directly from the form, all you need to do is assign a static `$model` variable to the <b>Form</b>.
</p>

<pre><code class="language-php">namespace App\Forms;

use App\Post;

class MyCustomForm extends \VlForm 
{
   public static $model = Post::class;
  
   //...</code></pre>

<p>To retrieve the instance of the Eloquent record in your Form, you may use the `record` method.</p>

<pre><code class="language-php">public function authorize(){
   //record() will return the Post instance
   return $this->record()->user_id == \Auth::user()->id;
}</code></pre>

<p> Note that this method can be used in all of the Forms' methods (`components`, `authorize` or `rules`, ...), except the `created` method that runs before retrieving the model from the DB.</p>

<!-- ------------------------------------------------------------- -->
<h3>CRUD operations</h3>

<h4>Create or Insert</h4>

  <p>When you simply need to insert a new Eloquent record, you may simply declare the class with the new keyword as you would any class in PHP.</p>

<pre><code class="language-php">class PostController extends Controller
{
    public function insertPost()
    {
        return view('insert',[
            'postForm' => new `PostForm`()
        ]);
    }
}</code></pre>

<h4>Update</h4>

  <p>When your is linked to an <b>Eloquent</b> model and wish to update the record, you may call the form with the static method `::find($id)`, where id is the record's id that you wish to edit - the same way you would call an Eloquent resource. For example:</p>

<pre><code class="language-php">class PostController extends Controller
{
    public function updatePost($id)
    {
        return view('update',[
            'postForm' => `PostForm`::find($id) // <- the $id of the Post
        ]);
    }
}</code></pre>

  <p>Submitting this form will automatically update the `App\Post` resource with the `$id` using the form data. It will also return a response with the updated instance.</p>

<h4>Delete</h4>

<p>
  If you use the <b>DeleteLink</b> component in your Form, you will only need to add a `deletable` method to your Eloquent model to handle the delete authorization and ensure that the user can delete the Eloquent model instance.
</p>

<pre><code class="language-php">class MyForm extends \VlForm
{
   public static $model = Post::class;

   public function components() { 
      return [
        DeleteLink::form($this->record())
      ];
   }
}</code></pre>

<p>
  And in your Eloquent model:
</p>

<pre><code class="language-php">class Post extends Model
{
   public function deletable() { 
      return $this->user_id == \Auth::user()->id;
   }
}</code></pre>

<!-- ------------------------------------------------------------- -->
<h3>Attributes & Relationships</h2>

<p>
    The <b>names of the fields</b> in the form are very important here and must match the related attribute (DB column name) or relationship (Eloquent method name). Behind the scenes, the form starts by checking if the name of the field is a column in the model's table. If it's not, it will check for a corresponding relationship method in your Eloquent Model. This allows it to automatically: 
</p>

<ul>
    <li><b>On display</b> - assign the model's attributes and load the relationships into the fields values.</li>
    <li><b>On submit</b> - save the fields attributes to the model's DB table and sync/associate/create the relationships according to the methods defined in your Eloquent Model.</li>
</ul>

<p>
    For example, if your Eloquent Model has a <b>title</b> attribute and a <b>tags</b> relationship like so:
</p>

<pre><code class="language-php">class Post extends Model
{
   //no need to specifiy anything for the title since it is a table column

   public function tags() 
   {
      return $this->belongsToMany(Tag::class);
   }
}</code></pre>

<p>
    This is all you need to save the title and sync the tags. Note that there is <u>no need to specify the type of relationship again</u> in the Form.
</p>

<pre><code class="language-php">class EloquentForm extends \VlForm
{
   public static $model = Post::class;
   
   public function components()
   {
      return [
         Input::form('Title'), //<-- this will assign a snake cased name of title

         MultiSelect::form('Enter one or more tags')
            ->name('tags')  //<-- this name matches the belongsToMany tags() method in the model
            ->optionsFrom('id','name'),

         Button::form('Save')->submitsForm()
      ];
   }
}</code></pre>

<h3>Eloquent helper methods</h3>

<p>
  When you need to retrieve your model or one of its' attributes in your form, you may use the helper methods `model` or `attribute`:
</p>

<pre><code class="language-php">class EloquentForm extends \VlForm
{
   public static $model = Post::class;
   
   public function components()
   {
      return [
         Link::icon('reply')->post('slug', ['slug' => $this->attribute('slug')]), //<-- the model's attribute value is obtained through the attribute method

         Rows::form(
            $this->record()->comments->each(function($comment) { //You may access the Eloquent model like so
              return Html::form($comment->comment);
            })
         ),

         Button::form('Save')->submitsForm()
      ];
   }
}</code></pre>




<!-- ------------------------------------------------------------- -->
<div class="wave"></div>
<!-- ------------------------------------------------------------- -->

    <h2>Form parameters</h2>

<!-- ------------------------------------------------------------- -->
<h3>Class properties</h3>

<p>
   The properties are useful for defining state and settings for the form. There a list of reserved properties that are used by vuravel and that you may override. For example:
</p>

<pre><code class="language-php">class MyForm extends \VlForm{
    //These properties can be overriden
    public $id = 'my-form-id';
    public $class = 'p-4 bg-gray-100'; 
    public $style = 'max-width:85vw;height:100px'; 

    protected $submitRoute = null; //Ex: 'subscription'. Use if the route is simple (no parameters)
    protected $submitMethod = 'POST'; //Accepts 'GET', 'PUT', etc...
    protected $redirectTo = null; //Ex: 'home'. Will redirect home.
    protected $redirectMessage = 'Success! Redirecting...'; //Will display a redirect message.

    protected $emitFormData = true; //If false: will not emit on submit.
    protected $preventSubmit = false; //If true: will not submit - only emit the form data.
    public static $refresh = false; //If true: will refresh the form after submit.

    //Reserved keywords - used by vuravel
    protected $blueprint;
    protected $classes;
    public $component;
    public $components;
    public $data;
    public $label;
    protected $store;
    protected $parameters;
    public $record;
    public $recordKey;
    public $recordKeyName;
    protected $routeObject;
    protected $styles;
    public $table;
    protected $validationRules;

    //Any other word can be used as a custom property
    public $customProperty
    ...

}</code></pre>


<!-- ------------------------------------------------------------- -->
<h3>Session store</h3>

<p>
    You will encounter many cases when you will need a form with one or more external parameters. The `store` method will allow us to <b>store an associative array of key/value variables in the "session" of the form</b>.
    For example, for an Answer to a parent Question, you may include the parent Question's id in the Form class:
</p>

<pre><code class="language-php">return view('update',[
   'answerForm' => AnswerForm::store(['question_id' => $questionId])
]);</code></pre>

<p>
    Then in your <b>Form</b> class, you can retrieve the preloaded data also thanks to the `store()` method.
</p>


<pre><code class="language-php">//AnswerForm class
public function components()
{
   return [
     Hidden::form('question_id')
        ->value( $this->store('question_id') )  // <-- $this->store($key) 
     ...
   ];
}
</code></pre>

@tip(You may store any type of object or class in the store but it is recommended that you store only strings or integers so that the session does not grow too big in size.)

<p>
    The reason why each Form class has a <b>store</b> that leverages PHP's session is: since the same Form class is used both for displaying it and then handling it's submission, we need to persist some information on the server in between the two stages.
</p>

<h4>Dependency injection</h4>

<p>
   Another helpful pattern is using the `created` method as a sort of "constructor" where you instantiate important objects that are used throughout your Form. For example:
</p>

<pre><code class="language-php">AddressForm::store([
   'sector_id' => $sectorId,
   'subsector_id' => $subsectorId
])
</code></pre>

<p>And in the `created` method, you may instantiate your objects like so:</p>

<pre><code class="language-php">class AddressForm extends \VlForm{

   public function created()
   {
      $this->sector = Sector::find($this->store('sectorId'));
      $this->subsector = Subsector::find($this->store('subsectorId'));
   }

   ...
</code></pre>

<!-- ------------------------------------------------------------- -->
<h3>Route parameters</h3>

<p>
    If you need to use one of the routes parameters, you may retrieve them with the `parameter` method from anywhere in the Form. For example:
</p>

<pre><code class="language-php">//Route for displaying AddressForm
Route::get('questions/{question_id}/answer/{id?}', 'AnswerController@writeAnswer');

class AnswerForm extends \VlForm{

   public function created()
   {
      $this->question = Question::find($this->parameter('question_id'));
   }

   ...
</code></pre>

@tip(The route parameters for displaying the Form are also usable during the submission phase, when the route may have changed.)

<!-- ------------------------------------------------------------- -->
<div class="wave"></div>
<!-- ------------------------------------------------------------- -->

    <h2>Displaying the form</h2>


<!-- ------------------------------------------------------------- -->
<h3>Render in Blade</h3>

<p>
    The `render` method will directly generate the vue component for you. The first line is nothing more than syntactic sugar for the second one.
</p>

<pre><code class="language-html" v-pre>&lt;!-- In your blade template -->
&#123;!! App\Forms\MyForm::render() !!}

&lt;!-- Same thing as this -->
&lt;vl-form :vcomponent="&#123;{ new App\Forms\MyForm() }}">&lt;/vl-form></code></pre>


<!-- ------------------------------------------------------------- -->
<h3>The Vue component</h3>

<p>
    The front-end component `vl-form` has one required prop `:vcomponent` prop where you inject the instantiated PHP Form class. Vuravel encodes it automatically to JSON.
</p>

<pre><code class="language-html" v-pre>&lt;vl-form :vcomponent="&#123;{ new App\Forms\MyForm() }}">&lt;/vl-form></code></pre>

@tip(Remember to place the `&lt;vl-form>` inside the bootable Vue.js element, which is usually the div with id `#app`.)

<!-- ------------------------------------------------------------- -->
<h4>Usage in Vue</h4>

<p>
    You may want to display the form inside one of your Vue components. In this case, just pass the form as a prop and use it in Vue.
</p>

<pre><code class="language-html" v-pre>&lt;!-- In your blade template -->
&lt;my-vue-component :form="&#123;{ new App\Forms\MyForm() }}">&lt;/my-vue-component></code></pre>

<pre><code class="language-html" v-pre>&lt;!-- MyVueComponent.vue -->
&lt;template>
   &lt;vl-form :vcomponent="form" @success="success">&lt;/vl-form>
&lt;/template>

&lt;script>
export default {
   props: ['form'],
   methods: {
     success(response){
       console.log(response)
     }
   }
}
&lt;/script>
</code></pre>

<!-- ------------------------------------------------------------- -->
<h3>Direct Route call</h3>

@include('docs.1-0.direct-route')

<!-- ------------------------------------------------------------- -->
<div class="wave"></div>
<!-- ------------------------------------------------------------- -->

<h2>Submitting the form</h2>

<p>
    To indicate which components will handle the submit functionnality, you may assign the `->submitsForm()` method to the component of your choice.
</p>
<p>The default behavior of this method is as follows:</p>

<ul>
  <li>If it's a <b>Trigger</b> (Button, Link, ...), it will submit on <b>click</b>,</li>
  <li>If it's a <b>Field</b> (Input, Select, ...), it will submit on <b>change</b>.</li>
</ul>

<pre><code class="language-php">Button::form('Save me')->submitsForm() //A Trigger will submit on click
Select::form('Pick a plan')->submitsForm() //A Field will submit on change
</code></pre>

<p>
   You may also make a field submit on input (while typing in the field) and debounce the request:
</p>

<pre><code class="language-php">//To debounce 400ms
Input::form('Name')->submitsOnInput(400)</code></pre>

<p>
   Note also that some fields (like Input, Password, ...) submit the Form on Enter by default. To disable this behavior, use:
</p>

<pre><code class="language-php">Password::form('Password')->dontSubmitOnEnter()</code></pre>

<!-- ------------------------------------------------------------- -->
<h3>Authorization</h3>

<p>
    To authorize a user to <b>submit</b> a Form, you may define an `authorize()` method, where you decide the security check for this action. For example, if you only want admins and the model's author to be able to submit a certain form:
</p>

<pre><code class="language-php">public function authorize()
{
  return \Auth::user()->admin || $this->record()->user_id == \Auth::user()->id;
}
</code></pre>

<p>
  Regarding the security of the display phase (i.e. who can view the Form), that should be handled by your middlewares in your routes or controllers.
</p>

@tip(By default, when a Form has no `authorize()` method, the security of the display phase ill be applied, meaning: if a user can see a certain Form, he will be able to submit it.)


<!-- ------------------------------------------------------------- -->
<h3>Validation</h3>

<p>
    Validating the input is very easy and uses <a href="https://laravel.com/docs/master/validation#available-validation-rules" target="_blank">Laravel's request validation rules</a>.<br> You just have to add the validation array to the `rules` method:

</p>

<pre><code class="language-php">public function rules()
{
   return [
      'first_name' => 'min:2|max:100',
      'last_name' => 'min:2|max:100',
      'nick_name' => 'required_without_all:last_name,first_name',
      'avatar|*' => 'sometimes|mimes:jpeg,jpg,png,gif|size:10000'
   ];
}
</code></pre>

<p>
    After an invalid form submit, an error response (coded 422) will be sent and the error messages will be displayed under the relevant components.
</p>

<pre><code class="language-json">`errors`: (Object)
  `first_name`: (Array)
    `0`: "The first name field is required."
  `last_name`: (Array)
    `0`: "The last name field is required."
`message`: "The given data was invalid."
</code></pre>

<h3>Handling Submission</h3>

  <p>If your Form is an Eloquent Form, you have nothing to do. The submission is handled automatically for you. You may change the response (see "Responses" section for more info).
  </p>

  <p>If however, you wish to handle the submission yourself, there are two ways to do so:</p>

  <h4>Handle method</h4>

<p>You may handle the <b>authorized and validated request</b> directly in your Form, using the `handle` method that gets the request as a parameter.</p>

<pre><code class="language-php">public function handle($request)
{
   //The $request is already "authorized" and "validated"
   //You may use it whichever way you wish
   dd($request->all());
}
</code></pre>

  <h4>Custom Route/Controller method</h4>
  <p>To submit a <b>Form</b> to a custom route and controller function, for example:</p> 

<pre><code class="language-php">Route::post('custom-route/{parameter}', 'MyController@myCustomForm')->name('my-custom-route');</code></pre>

  <p>You may define the desired target route for submission in the `submitUrl()` method of your <b>Form</b> class:</p>

<pre><code class="language-php">class MyForm extends \VlForm 
{
  //For a Route with no parameters, you may use:
  protected $submitRoute = 'my-custom-route';
  
  //For a Route with parameters, you may use:
  public function submitUrl()
  {
    return route('my-custom-route', ['parameter' => 'some-value']);
  }</code></pre>

<p>Then in your controller, you can retrieve the <a href="#validating-input">validated</a> and <a href="#authorizing-submission">authorized</a> `FormValidationRequest` (which is an extension of Laravel's native `Illuminate\Foundation\Http\FormRequest`).</p>

<pre><code class="language-php">use Vuravel\Form\Http\Requests\FormValidationRequest;

class MyController extends Controller
{
    public function myCustomForm(FormValidationRequest $request)
    {
        dd($request->all());
    }</code></pre>

  <h4>Emitting an event</h4>

  <p>You might not want the form to submit to the backend at all, but rather to simply emit an event to it's parent vue component for example. To do so, you can set the property `emitFormData` in your <b>Form</b> class:</p>

<pre><code class="language-php">public $emitFormData = true;</code></pre>

  <p>The emitted response can be captured with the event handler `@submit` or `v-on:submit` in the parent vue component:</p>

<pre><code class="language-html">&lt;template>
    &lt;vl-form :vcomponent="vcomponent" @submit="performAction">&lt;/vl-form>
&lt;/template>

&lt;script>
export default {
    props: ['vcomponent'],
    methods: {
        performAction(formData) {
            console.log(formData)
        }
    }
}
&lt;/script>
</code></pre>

<h2>Responses</h2>
  <p>There are multiple ways to handle a respone form the form, and the most common ones are already preconfigured.</p>

  <h3>Emit event on success</h3>

  <p>If a successful response is received from the server, the form will emit a `success` event that could be listened to from it's parent component. The event will receive the server's response object as the parameter: </p>

<pre><code class="language-html">&lt;template>
    &lt;vl-form :vcomponent="vcomponent" @success="performAction">&lt;/vl-form>
&lt;/template>

&lt;script>
export default {
    props: ['vcomponent'],
    methods: {
        performAction(response) {
            console.log(response)
        }
    }
}
&lt;/script>
</code></pre>

  <h3>Redirect after submit</h3>
  <p>To redirect after submitting a form, you have two options:</p>

  <h4>Redirect to a specific Route in the Controller</h4>

  <p>The simplest way to redirect after a form submit is to use a Laravel's <a href="https://laravel.com/docs/master/responses#redirects" target="_blank">`RedirectResponse` instance</a>, meaning any one of these methods will work:</p>

<pre><code class="language-php">&lt;?php

namespace App\Http\Controllers;

use Vuravel\Form\Http\Requests\VuravelRequest;

class MyController extends Controller
{

    public function myCustomForm(VuravelRequest $request)
    {
        return redirect('home');
        // Any one of these would work too:
        //return redirect()->route('profile');
        //return back();
        //return url()->previous();
    }

}

</code></pre>

  <h4>Preconfigured redirect Route in the Form Class</h4>

  <p>Alternatively, if you are using an Eloquent Form for example, you can assign the static `$redirectTo` property in your <b>Form</b> class. This will redirect to that route and display an optional redirect message.</p>

<pre><code class="language-php">&lt;?php

namespace App\Forms;

use Vuravel\Form\Form;

class MyCustomForm extends Form 
{
  protected $redirectTo = 'home';

  //...
}
</code></pre>

  <h4>Redirect message</h4>
  <p>You can define a redirect message that will be displayed in the Form Status box using the static `$redirectText` property. Note that this string will be translated using the `__()` function if your app supports multiple languages.</p>

<pre><code class="language-php">&lt;?php

namespace App\Forms;

use Vuravel\Form\Form;

class MyCustomForm extends Form 
{
  protected $redirectText = 'Success! Redirecting...';

  //...
}
</code></pre>

  <p>In this example, the translated content will need to be configured in the `lang/{locale}.json` file with the key 'Success! Redirecting...'.</p>


<!-- ------------------------------------------------------------- -->
<div class="wave"></div>
<!-- ------------------------------------------------------------- -->

<h2>Custom components</h2>

  <p>If needed, you can easily extend the list of available components and use them in your forms by adding your own custom component. Let us know about it as we will be constantly adding more components as the project grows.</p>

<!-- ------------------------------------------------------------- -->
  <h3>Creating the PHP class</h3>

  <p>First, the easy part: creating the PHP class for this component. This class should use the trait of the behavior it corresponds to (`IsAttributeByDefault`, `IsLayoutComponent`, `IsActionComponent`).</p>

  <p>A very important property is the <b>$component</b>. `vl-form` will look for a `vl-{$component}` and will load the PHP class public properties into it (see next section for more details on how to set-up this component)</p>

<pre><code class="php">&lt;?php

namespace App\Forms\Components;

use Vuravel\Form\Component;

class CustomComponent extends Component
{ 
    // Use this Trait if it's a Field Component...
    use \Vuravel\Form\Traits\IsAttributeByDefault;

    // Or if it's a Layout Component...
    // use \Vuravel\Form\Traits\IsLayoutComponent;

    //vl-form will look for `&lt;vl-custom-component />`
    public $component = 'custom-component';

}
</code></pre>


  <h4>Data attribute</h4>

  <p>One of the more used features in `vuravel/form` is the `->data()` method which allows you to send all kind of data to the Front-End component. It accepts an associative array as argument: </p>

<pre><code class="language-php">Input::form('First Name')->data(['info' => 'Your first name as written on your Passport'])</code></pre>

  <p>You will find more information on the different ways to make use of the `data()` method in the section with the list of available components.</p>


<!-- ------------------------------------------------------------- -->
  <h3>Creating the Vue.js component</h3>

  <p>You should create a `VlCustomComponent.vue` file with the code below. Note that you can add attributes and event bindings by overriding the `$_attributes` and `$_events` methods from the mixin.</p> 

<pre><code class="javascript">&lt;template>
    &lt;vl-form-field :component="component">
        &lt;input
            v-model="component.value"
            class="form-control"
            v-bind="$_attributes"
            v-on="$_events"
        />
    &lt;/vl-form-field>
&lt;/template>

&lt;script>
import Field from '../mixins/Field'
export default {
    mixins: [Field],
    computed: {
      $_attributes() {
        return {
          ...this.$_defaultFieldAttributes,
          anotherattribute: 'some value'
        }
      },
      $_events() {
        return {
          ...this.$_defaultFieldEvents,
          anotherevent: this.someMethod
        }
      }
    }
}
&lt;/script>
</code></pre>

@endsection
