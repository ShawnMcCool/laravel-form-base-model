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
	}

**Example controller usage:**

	Route::get('user/(:num)', function($id)
	{
		$user = DB::table('users')->find($id);

		return View::make('profile')->with('user', $user);
	});

**Redirecting & Flashing Data To The Session:**

	return Redirect::to('profile')->with('message', 'Welcome Back!');
