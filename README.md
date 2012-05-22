## A form base-model for Laravel

Forms often are used to interact with a specific type of data such as a user or a blog post. However, in many circumstances a form may collect data that is related to multiple types of data. Forms may also have special validation requirements that have little to do with the underlying data, such as captcha. Consequently, it often makes sense to create a form model.

This form base-model is currently in development. It is very likely that this class will be continuously refactored over time to support more use-cases. This project is open for community participation so please feel free to submit issues and/or pull-requests.

### Feature Overview

- Form validation mechanism.
- Persistent form-data management for multi-page forms.
- Interface for accessing persistent form-data.

### Examples

**Example form model:**

	class ExampleForm extends FormBase_Model
	{

		public static $rules = array(
			'first_name' => 'required',
			'status'     => 'required',
		);

		public static $status = array(
			'' => 'Choose One',
			1  => 'Active',
			2  => 'inactive',
			3  => 'Deleted',
			4  => 'Boring',
			5  => 'Adventurer',
		);

		public static $foods = array(
			1 => 'Tacos',
			2 => 'Chicken Tacos',
			3 => 'Beef Tacos',
			4 => 'Vegetarian Tacos',
			5 => 'Black Bean Nachos',
		);

	}

**Example controller usage:**

	// simple form example

	public function get_simple_example()
	{

		return View::make( 'form-base-model::simple_example' );

	}

	public function post_simple_example()
	{

		// define which fields this request will validate and save

		$fields = array( 'first_name', 'last_name', 'status' );

		if( ExampleForm::is_valid( $fields ) )
		{
			
			ExampleForm::save_input( $fields );
			
			return Redirect::to_route( 'form_examples', array( 'simple_example_review' ) );

		}
		else
			return Redirect::back()->with_input()->with_errors( ExampleForm::$validation );

	}

**Populating form views:**

The populate() method will fill a form field with the value stored in the persistent form data. It supports an optional second parameter that represents the default value which should be returned when the requested field data is empty. If input flash data exists (should there be validation errors and the user is redirected to the form to correct them) then the flash data will be used instead.

	First Name: {{ Form::text( 'first_name', ExampleForm::populate( 'first_name' ) ) }}

**Retrieve form data for processing**

The get() method returns the data for the requested form field and supports and optional second parameter that represents the default value which should be returned when the requested field data is empty.

	ExampleForm::get( 'status', 'none entered' );

The all() method returns an array of all field data.

	ExampleForm::all();