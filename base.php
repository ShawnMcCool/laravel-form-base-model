<?php namespace FormBaseModel;

/**
 * form-base-model : A form base model for use with the Laravel PHP framework.
 *
 * @package  laravel-form-base-model
 * @version  1.3
 * @author   Shawn McCool <shawn@heybigname.com>
 * @link     https://github.com/shawnmccool/laravel-form-base-model
 */

abstract class Base
{
	/**
	 * The internal field_data array used for storing persistent data
	 * across multi-page forms.
	 *
	 * @var array
	 */
	public static $field_data = array();

	/**
	 * The rules array stores Validator rules in an array indexed by
	 * the field_name to which the rules should be applied.
	 *
	 * @var array
	 */
	public static $rules = array();

	/**
	 * The messages array stores Validator messages in an array indexed by
	 * the field_name to which the messages should be applied in case of errors.
	 *
	 * @var array
	 */
	public static $messages = array();

	/**
	 * The validation object is stored here once is_valid() is run.
	 * This object is publicly accessible so that it can be used
	 * to redirect with errors.
	 *
	 * @var object
	 */
	public static $validation = false;

	/**
	 * If an array or object is loaded into the form model using the load() method
	 * the $loaded variable will contain that original array or object.
	 * 
	 * @var object
	 */
	public static $loaded = null;

	/**
	 * True if custom validators have been loaded.
	 * 
	 * @var object
	 */
	public static $custom_validators_loaded = null;

	/**
	 * This method can be overridden in order to add custom validators, generate
	 * custom validation messages with expressions, or whatever.
	 */
	public static function before_validation()
	{
	}

	/**
	 * Resets the form model to its vanilla form. Mostly used for tests.
	 */
	public static function reset()
	{
		static::$field_data               = array();
		static::$rules                    = array();
		static::$messages                 = array();
		static::$validation               = false;
		static::$loaded                   = null;
		static::$custom_validators_loaded = null;
	}

	/**
	 * Validates input data. Only fields present in the $fields array
	 * will be validated. Rules must be defined in the form model's
	 * static $rules array.
	 *
	 * <code>
	 * 		// define rules within the form model
	 * 		public static $rules = array( 'first_name' => 'required' );
	 *
	 *		// validate fields from the controller
	 *		$is_valid = ExampleForm::is_valid( array( 'first_name', 'last_name' ));
	 * </code>
	 *
	 * Tested
	 * 
	 * @param  array   $fields
	 * @param  array   $input
	 * @return bool
	 */
	public static function is_valid( $fields = null, $input = null )
	{
		// run before_validation hook
		static::before_validation();

		// $fields must be an array or null, a null value represents
		// that all fields should be validated
		if( !is_array( $fields ) && !is_null( $fields ))
		{
			return false;
		}

		// if input is null then pull all input from the input class
		if( is_null( $input ))
		{
			$input = \Input::all();
		}

		// if $fields is an array then we need to walk through the
		// rules defined in the form model and pull out any that
		// apply to the fields that were defined
		if( is_array( $fields ))
		{
			$field_rules = array();

			foreach( $fields as $field_name )
			{
				if( array_key_exists( $field_name, static::$rules ))
				{
					$field_rules[$field_name] = static::$rules[$field_name];
				}
			}
		}
		else
		{
			// if $fields isn't an array then apply all rules
			$field_rules = static::$rules;
		}

		// if no rules apply to the fields that we're validating then
		// validation passes
		if( empty( $field_rules ))
		{
			return true;
		}

		// remove empty rules
		foreach( $field_rules as $field => $rules )
		{
			if( empty( $rules ))
			{
				unset( $field_rules[$field] );
			}
		}

		// generate the validator and return its success status
		static::$validation = \Validator::make( $input, $field_rules, static::$messages );

		return static::$validation->passes();
	}

	/**
	 * Serialize the model's field data to the session.
	 * 
	 * Tested
	 */
	public static function serialize_to_session()
	{
		$class_name   = get_called_class();
		$session_name = 'serialized_field_data[' .$class_name. ']';

		// does this class have field data to serialize?
		if( !isset( static::$field_data[$class_name] ) || empty( static::$field_data[$class_name]->attributes ))
		{
			// if not, serialize an empty array
			$serialized_data = serialize( array() );
		}
		else
		{
			// otherwise, serialize the stored field data
			$serialized_data = serialize( static::$field_data[$class_name]->attributes );
		}

		\Session::put( $session_name, $serialized_data );
	}

	/**
	 * Unserialize the model's field data from a session
	 * 
	 * Tested
	 */
	public static function unserialize_from_session()
	{
		$class_name   = get_called_class();
		$session_name = 'serialized_field_data[' .$class_name. ']';

		if( \Session::has( $session_name ))
		{
			// there seems to be serialized data related to this class,
			// let's load it up
			$data = unserialize( \Session::get( $session_name ));

			static::$field_data[$class_name] = new \Laravel\Fluent( $data );
		}
		else
		{
			// initialize static::$field_data
			static::$field_data[$class_name] = new \Laravel\Fluent;
		}
	}

	/**
	 * Saves input from the $input parameter (array) into the form model's
	 * field_data array if the key is present in the $fields array then
	 * serializes the field_data array to the session.
	 *
	 * The $fields array is a simple array. Only the fields declared in
	 * the $field array will be stored.
	 *
	 * <code>
	 *		// save form input data
	 *		ExampleForm::save_input( array( 'first_name', 'last_name' ));
	 * </code>
	 * 
	 * Tested
	 * 
	 * @param  array   $fields
	 * @param  array   $input
	 */
	public static function save_input( $fields = null, $input = null )
	{
		$class_name = get_called_class();

		// $fields must be an array
		if( !is_array( $fields ) && !is_null( $fields ))
		{
			return false;
		}

		// by default we save all fields
		if( is_null( $fields ))
		{
			$fields = array_keys( \Input::all() );
		}

		// by default we save all input, this can be overridden by passing
		// a second parameter to the save_input() method
		if( is_null( $input ))
		{
			// create a fluent class containing the input data from a get/post
			$input = new \Laravel\Fluent( \Input::all() );
		}
		else
		{
			// create a fluent class containing the input data from the method argument
			$input = new \Laravel\Fluent( $input );
		}

		// when storing input it's important to load the persistent form
		// data that may exist from previous requests, otherwise we will
		// overwrite them
		if( !isset( static::$field_data[$class_name] ) || empty( static::$field_data[$class_name]->attributes ))
		{
			static::unserialize_from_session();
		}

		// ideally we'll have either a value for a field or an empty value
		// for a field. this isn't strictly necessary and may change in the
		// future given an appropriately convincing argument
		foreach( $fields as $field_name )
		{
			// assign a value in the internal field_data store
			static::set( $field_name, $input->$field_name );	
		}

		// serialize the field data to session
		static::serialize_to_session();
	}

	/**
	 * Empty persistent form field_data.
	 * 
	 * Tested
	 */
	public static function forget_input()
	{
		$class_name   = get_called_class();
		$session_name = 'serialized_field_data[' .get_called_class(). ']';

		// remove the persistent form data FOR-EV-ER, FOR-EV-ER, FOR..
		\Session::forget( $session_name );
	}

	/**
	 * Determine if the form's field_data data contains an item.
	 *
	 * If the field doesn't exist in the field_data, false will be returned.
	 *
	 * Tested
	 * 
	 * @param  string  $field_name
	 * @return bool
	 */
	public static function has( $field_name )
	{
		$class_name = get_called_class();

		return isset( static::$field_data[$class_name] ) && static::$field_data[$class_name]->$field_name;
	}

	/**
	 * Set a value in the form's field data.
	 *
	 * <code>
	 *		// Set the email's value
	 *		ExampleForm::set( 'email', 'shawn@heybigname.com' );
	 * </code>
	 *
	 * Tested
	 * 
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return mixed
	 */
	public static function set( $key, $value )
	{
		$class_name = get_called_class();

		// prevent need to manually load input when populating forms
		if( !isset( static::$field_data[$class_name] ) || empty( static::$field_data[$class_name]->attributes ))
		{
			static::unserialize_from_session();
		}

		return static::$field_data[$class_name]->$key = $value;
	}

	/**
	 * Load an object or array into the form model's persistent field_data store
	 *
	 * <code>
	 *		// Load an Eloquent model
	 * 		$user = User::find( 1 );
	 *		ExampleForm::load( $user );
	 * 
	 * 		// Load an array
	 * 		ExampleForm::load( array('best movie ever' => 'Primer' ));
	 * </code>
	 * 
	 * Tested 
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return mixed
	 */
	public static function load( $data )
	{
		$class_name = get_called_class();

		static::$loaded = $data;

		if( $data instanceof \Eloquent )
		{
			return static::$field_data[$class_name] = new \Laravel\Fluent( $data->attributes );
		}

		return static::$field_data[$class_name] = new \Laravel\Fluent( $data );
	}

	/**
	 * Returns true if the form has been loaded with data.
	 * 
	 * Tested
	 * 
	 * @return bool
	 */
	public static function loaded()
	{
		return !is_null( static::$loaded );
	}

	/**
	 * Get an item from the form's field data.
	 *
	 * <code>
	 *		// Get the "email" item from the form's field data array
	 *		$email = ExampleForm::get( 'email' );
	 *
	 *		// Get the "email" and "first_name" items as an array
	 *		$email = ExampleForm::get( array( 'email', 'first_name' ));
	 *
	 *		// Return a default value if the specified item doesn't exist
	 *		$email = ExampleForm::get( 'email', 'not listed' );
	 * </code>
	 *
	 * Tested
	 * 
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public static function get( $fields, $default = null )
	{
		$class_name = get_called_class();

		// prevent need to manually load input when populating forms
		// by unserializing here
		if( !isset( static::$field_data[$class_name] ) || empty( static::$field_data[$class_name]->attributes ))
		{
			static::unserialize_from_session();
		}

		// if we request a single field, deliver that
		if( !is_array( $fields ))
		{
			return static::has( $fields ) ? static::$field_data[$class_name]->$fields : $default;
		}

		// create an array that'll hold fields that we'll be returning
		$return_fields = array();

		foreach( $fields as $field )
		{
			if( static::has( $field ))
			{
				$return_fields[$field] = static::get( $field );
			}
		}

		return $return_fields;
	}

	/**
	 * Get all of the persistent form data.
	 *
	 * Tested
	 * 
	 * @return array
	 */
	public static function all()
	{
		$class_name = get_called_class();

		if( !isset( static::$field_data[$class_name] ) || empty( static::$field_data[$class_name]->attributes ))
		{
			static::unserialize_from_session();
		}
		
		return static::$field_data[$class_name]->attributes;
	}

	/**
	 * Use to populate a form field. Loads the field's value from
	 * flashed input data, if that's not present it serves up the
	 * default value
	 *
	 * <code>
	 *   echo Form::text( 'first_name', ExampleForm::old( 'first_name' ));
	 *	 echo Form::text( 'number_of_turtles', ExampleForm::old( 'number_of_turtles', 4 ));
	 * </code>
	 *
	 * Tested
	 * 
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public static function old( $fields, $default = null )
	{
		$class_name = get_called_class();

		// prevent need to manually load input when populating forms
		if( !isset( static::$field_data[$class_name] ) || empty( static::$field_data[$class_name]->attributes ))
		{
			static::unserialize_from_session();
		}

		// return input flash data, fallback on persistent for data, fallback on default
		$hierarchical_data = new \Laravel\Fluent( array_merge( static::all(), \Input::old() ));

		// if we're not requesting multiple fields let's just return a scalar
		if( !is_array( $fields ))
		{
			return !is_null( $hierarchical_data->$fields ) ? $hierarchical_data->$fields : $default;
		}

		// we're returning multiple fields so they'll need to be sent as an array
		$return_fields = array();

		foreach( $fields as $field )
		{
			$return_fields[$field] = !is_null( $hierarchical_data->$field ) ? $hierarchical_data->$field : $default;	
		}

		return $return_fields;
	}

	/**
	 * Use to populate a checkbox form field. Use this for checkboxes instead
	 * of old(). The first parameter is the field name, the second is the value
	 * of the checkbox. The third is a boolean that determines the default
	 * state of the checkbox.
	 *
	 * <code>
	 * 		echo Form::checkbox( 'active', 1, ExampleForm::old_checkbox( 'active', $user->active, true ));
	 * </code>
	 * 
	 * Tested
	 *
	 * @param  string  $field
	 * @param  string  $value
	 * @param  mixed   $default
	 * @return mixed
	 */
	public static function old_checkbox( $field, $value, $default = false )
	{
		// return input flash data, fallback on persistent for data, fallback on default
		$hierarchical_data = new \Laravel\Fluent( array_merge( static::all(), \Input::old() ));

		if( $hierarchical_data->$field )
		{
			if( is_array( $hierarchical_data->$field ))
			{
				return in_array( $value, $hierarchical_data->$field ) ? true : $default;
			}
			else
			{
				return $hierarchical_data->$field == $value ? true : $default;
			}

		}
		else
		{
			return $default;
		}
	}
}