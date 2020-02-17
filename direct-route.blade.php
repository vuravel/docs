<p>
    You may also call a vuravel Form or Catalog directly from a route without the need of passing through a controller or view. If you've defined a vuravel <a href="{{route('docs', ['p' => 'menus']) }}#displaying-the-menus" target="_blank">Blade layout template</a>, you may display the component thanks to the `Route::vuravel` macro.
</p> 

<pre><code class="language-php">//routes/web.php

//To load components in our vuravel Blade template 'my-template.blade.php':	
Route::group(['extends' => 'my-template'], function(){
   Route::vuravel('questions/{question_id}/answer/{id?}', AnswerForm::class);  
   Route::vuravel('profile/change_password', ChangePasswordForm::class);
   ... //you may add as many components that share the same layout
});

//To only load the component with no template:
Route::vuravel('questions/{question_id}/answer/{id?}', AnswerForm::class);
</code></pre>

<p>
	The route inside the `extends` group will display the form in the "app" template along with any navbar or sidebars it may contain. The other route will only display the form in a panel for example. This is useful when doing AJAX requests and/or loading modals.
</p>