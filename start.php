<?php

// register the class

Autoloader::map(array(
    'FormBase_Model' => __DIR__.'/formbase_model.php',
));

// register the example code

Autoloader::map(array(
	'ExampleForm' => __DIR__.'/models/exampleform.php',
));

Route::get( 'form_example', function()
{

	ExampleForm::load_input();

	return View::make( 'form-base-model::form_one' );

});

Route::post( 'form_example', function()
{

	$fields = array( 'first_name', 'last_name', 'status' );

	ExampleForm::load_input();

	if( ExampleForm::is_valid( $fields ) )
	{
		
		ExampleForm::store_input( $fields );
		
		return Redirect::to( 'form_example_page_two' );

	}
	else
		return Redirect::back()->with_input()->with_errors( ExampleForm::$validation );

});

Route::get( 'form_example_page_two', function()
{

	ExampleForm::load_input();

	return View::make( 'form-base-model::form_two' );

});

Route::post( 'form_example_page_two', function()
{

	$fields = array( 'street_address', 'suite_number' );

	if( ExampleForm::is_valid( $fields ) )
	{
		
		ExampleForm::store_input( $fields );
		
		return Redirect::to( 'form_example_page_review' );

	}
	else
		return Redirect::back()->with_input()->with_errors( ExampleForm::$validation );

});

Route::get( 'form_example_page_review', function()
{

	ExampleForm::load_input();

	return View::make( 'form-base-model::form_review' );

});