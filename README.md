## A Form Base-Model for Laravel

**Version: 1.4**

Forms are often used to interact with a specific model such as a user or a blog post. However, in many circumstances a form may collect data that is related to multiple data models. Consequently, may also have special validation requirements that have little to do with the underlying data, such as captcha and password confirmation. Consequently, it often makes sense to create a form model. A form model represents the data needs of a form. This may be validation alone, storing values for form select drop-downs, having custom methods to generate data for the form, or managing persistent data in a session to make multi-page forms simple.

This form base-model is currently in development. It is very likely that this class will be continuously refactored over time to support more use-cases. This project is open for community participation so please feel free to submit issues and/or pull-requests.

[A video explaining the usage of a form model can be found here.](http://heybigname.com/2012/05/22/introduction-to-the-form-model/)

### Feature Overview

- Form validation mechanism.
- Simple multi-page form management.
- Simple form field re-population.
- A "before validation" hook that allows for custom validator registration, rule updates, etc.

### Recent Changes

**1.4**
- class in now abstract
- added before_validation() method for registering validators, updating rules, etc
- improved example code

**1.3**
- Heavy refactoring of the class
- Namespaced to FormBaseModel
- Class renamed to Base
- Added unit-testing

**1.2**
- Added $loaded attribute which is populated by whatever object or array is sent to the load() method.
- Added old_checkbox() method which is used to populate checkboxes from old input. More functionality will be added to this method once I or someone else decides on an appropriate algorithm.

### Installation

Install with artisan

	php artisan bundle:install form-base-model

or, clone the project into **bundles/form-base-model**.

Then, update your bundles.php to auto-start the bundle.

	return array(
		'form-base-model' => array( 'auto' => true ),
	);

**Notice:** The form-base-model bundle comes with examples that will auto-route to http://yoursite/form_examples. Remember to remove this from bundles/form-base-model/start.php when you're finished with the examples.

### Examples

**Note:** More documentation can be found in base.php.

**Example form model:**

	class ExampleForm extends FormBaseModel\Base
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

		public static function before_validation()
		{
			// register custom validators, modify rules, etc
		}
	}

**Simple example controller usage:**

	// simple form example
	public function get_simple_example()
	{
		return View::make( 'form-base-model::simple_example' );
	}

	public function post_simple_example()
	{
		if( !ExampleForm::is_valid() )
		{
			return Redirect::back()->with_input()->with_errors( ExampleForm::$validation );
		}

		return Redirect::to_route( 'form_examples', array( 'simple_example_review' ));
	}

**Multi-page form example controller usage:**

	public function get_multi_page_example()
	{
		// clear any lingering persistent data as we generate a new form instance
		ExampleForm::forget_input();

		return Redirect::to_route( 'form_examples', array( 'multi_page_example_one' ));
	}

	public function get_multi_page_example_one()
	{
		return View::make( 'form-base-model::multi_page_example_one' );
	}

	public function post_multi_page_example_one()
	{
		// define which fields this request will validate and save
		$fields = array( 'first_name', 'last_name', 'status' );

		// validate form and redirect with errors on failure
		if( !ExampleForm::is_valid( $fields ))
		{
			return Redirect::back()->with_input()->with_errors( ExampleForm::$validation );
		}

		// save input to session
		ExampleForm::save_input( $fields );
		
		// redirect to the next page
		return Redirect::to_route( 'form_examples', array( 'multi_page_example_two' ));
	}
	
	public function get_multi_page_example_two()
	{
		return View::make( 'form-base-model::multi_page_example_two' );
	}

	public function post_multi_page_example_two()
	{
		$fields = array( 'street_address', 'suite_number', 'favorite_foods' );
		
		if( !ExampleForm::is_valid( $fields ))
		{		
			return Redirect::back()->with_input()->with_errors( ExampleForm::$validation );
		}

		// save input to session
		ExampleForm::save_input( $fields );
		
		// redirect to review page
		return Redirect::to_route( 'form_examples', array( 'multi_page_example_review' ));
	}


**Populating form views:**

The old() method will fill a form field with the value stored in the persistent form data. It supports an optional second parameter that represents the default value which should be returned when the requested field data is empty. If input flash data exists (should there be validation errors and the user is redirected to the form to correct them) then the flash data will be used instead.

	First Name: {{ Form::text( 'first_name', ExampleForm::old( 'first_name' )) }}

**Retrieve form data for processing**

The get() method returns the data for the requested form field and supports and optional second parameter that represents the default value which should be returned when the requested field data is empty.

	ExampleForm::get( 'status', 'none entered' );

**Load data into the form model**

Use the load() method to populate form data. In this way it's possible to use the same form code for add and edit forms. One simple would load in the data before returning the form.

	// load from array
	ExampleForm::load( array( 'first_name', 'last_name' ));

	// load from eloquent
	$user = User::find( 1 );

	ExampleForm::load( $user );

**Note:** It's important to point out that get() relies on data saved with save_input(). If you aren't using the persistent data just use Input::get();

The all() method returns an array of all field data.

	ExampleForm::all();

To clear persistent data from a form model just tell it to forget the input.

	ExampleForm::forget_input();
	
**Saving form data to Eloquent model**

When it's time to save the data from your form to your database just access the form the same way you'd access data using Laravel's Input class. 

**Note:** This example will only return data from saved form data. If your form is a simple form and deosn't save persistent data then use Input::get() or Input::only().

	$user = new User;
	
	$user->first_name = ExampleForm::get( 'first_name' );
	$user->last_name  = ExampleForm::get( 'last_name' );
	
	$user->save();

	// alternatively you can return the data as an array
	$user = new User( ExampleForm::get( array( 'first_name', 'last_name' )));

**Example of a properly formed controller action that handles form post data.**

	public function post_add()
	{
		if( !ProgramForm::is_valid() )
		{
			return Redirect::back()->with_input()->with_errors( ProgramForm::$validation );
		}

		$program = new Program( Input::only( array( 'title', 'description' )));

		// here i'm using my eloquent base model to validate
		// https://github.com/ShawnMcCool/laravel-eloquent-base-model
		if( !$program->is_valid() )
		{
			return Redirect::back()->with_input()->with_errors( $program->validation );
		}

		$program->save();

		return Redirect::to_action( 'admin.programs@index' )->with( 'success', 'The program ' . $program->title . ' has been added.' );
	}