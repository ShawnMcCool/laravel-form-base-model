<?php

class Form_Base_Model_Examples_Controller extends Controller
{

	public $restful = true;

	public function get_index()
	{

		return View::make( 'form-base-model::index' );

	}

	// simple form example

	public function get_simple_example()
	{

	}

	public function post_simple_example()
	{

	}

	public function get_simple_example_review()
	{

	}

	// multiple page form example

	public function get_multi_page_example_one()
	{

		// load form data (so that the form will be populated)

		ExampleForm::load_input();

		// display the form

		return View::make( 'form-base-model::multi_page_example_one' );

	}

	public function post_multi_page_example_one()
	{

		// define which fields this request will validate and store

		$fields = array( 'first_name', 'last_name', 'status' );

		// ExampleForm::load_input();

		if( ExampleForm::is_valid( $fields ) )
		{
			
			ExampleForm::store_input( $fields );
			
			return Redirect::to( 'multi_page_example_two' );

		}
		else
			return Redirect::back()->with_input()->with_errors( ExampleForm::$validation );

	}
	
	public function get_multi_page_example_two()
	{

		// load form data (so that the form will be populated)

		ExampleForm::load_input();

		return View::make( 'form-base-model::multi_page_example_two' );

	}

	public function post_multi_page_example_two()
	{
		
		$fields = array( 'street_address', 'suite_number' );

		if( ExampleForm::is_valid( $fields ) )
		{
			
			ExampleForm::store_input( $fields );
			
			return Redirect::to( 'form_example_page_review' );

		}
		else
			return Redirect::back()->with_input()->with_errors( ExampleForm::$validation );

	}

	public function get_multi_page_example_review()
	{

		// load form data (so that the page will be populated)
		ExampleForm::load_input();

		return View::make( 'form-base-model::multi_page_example_review' );

	}
	
}