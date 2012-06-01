<?php

class FormBase_Model
{

	/**
	 * The internal field_data array used for storing persistent data
	 * across multi-page forms.
	 *
	 * @var array
	 */
	protected static $field_data = array();

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
	 * Validates input data. Only fields present in the $fields array
	 * will be validated. Rules must be defined in the form model's
	 * static $rules array.
	 *
	 * <code>
	 * 		// define rules within the form model
	 * 		public static $rules = array( 'first_name' => 'required' );
	 * 
	 *		// validate fields from the controller
	 *		$is_valid = ExampleForm::is_valid( array( 'first_name', 'last_name' ) );
	 * </code>
	 *
	 * @param  array   $fields
	 * @param  array   $input
	 * @return bool
	 */
	public static function is_valid( $fields = null, $input = null )
	{

		// $fields must be an array or null, a null value represents
		// that all fields should be validated

		if( !is_array( $fields ) && !is_null( $fields ) )
			return false;

		// if input is null then pull all input from the input class

		if( is_null( $input ) )
			$input = Input::all();

		// if $fields is an array then we need to walk through the
		// rules defined in the form model and pull out any that
		// apply to the fields that were defined

		// if $fields isn't an array then apply all rules

		if( is_array( $fields ) )
		{
			
			$field_rules = array();

			foreach( $fields as $field_name )
				if( array_key_exists( $field_name, static::$rules ) )
					$field_rules[$field_name] = static::$rules[$field_name];

		}
		else
			$field_rules = static::$rules;

		// if no rules apply to the fields that we're validating then
		// validation passes

		if( empty( $field_rules) )
			return true;

		// generate the validator and return its success status

		static::$validation = Validator::make( $input, $field_rules, static::$messages );

		return static::$validation->passes();

	}

	/**
	 * Serialize the model's field data to the session.
	 */
	private static function serialize_to_session()
	{

		Session::put( 'serialized_field_data[' .get_called_class(). ']', serialize( static::$field_data ) );

	}

	/**
	 * Unserialize the model's field data from a session
	 */
	private static function unserialize_from_session()
	{

		if( Session::has( 'serialized_field_data[' .get_called_class(). ']' ) )
			static::$field_data = unserialize( Session::get( 'serialized_field_data[' .get_called_class(). ']' ) );

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
	 *		ExampleForm::save_input( array( 'first_name', 'last_name' ) );
	 * </code>
	 *
	 * @param  array   $fields
	 * @param  array   $input
	 */
	public static function save_input( $fields = null, $input = null )
	{

		// $fields must be an array

		if( !is_array( $fields ) && !is_null( $fields) )
			return false;

		// by default we save all fields

		if( is_null( $fields ) )
			$fields = array_keys( Input::all() );

		// by default we save all input, this can be overridden by passing
		// a second parameter to the save_input() method

		if( is_null( $input ) )
			$input = Input::all();

		// when storing input it's important to load the persistent form 
		// data that may exist from previous requests, otherwise we will
		// overwrite them

		if( empty( static::$field_data ) )
			static::unserialize_from_session();

		// ideally we'll have either a value for a field or an empty value
		// for a field. this isn't strictly necessary and may change in the
		// future given an appropriately convincing argument

		foreach( $fields as $field_name )
		{
			if( Input::has( $field_name ) )
				static::$field_data[$field_name] = Input::get( $field_name );
			else
				static::$field_data[$field_name] = '';

		}

		// serialize the field data to session

		static::serialize_to_session();

	}

	/**
	 * Empty persistent form field_data.
	 */
	public static function forget_input()
	{

		// remove the persistent form data FOR-EV-ER, FOR-EV-ER, FOR..
		
		Session::forget( 'serialized_field_data[' .get_called_class(). ']' );

	}

	/**
	 * Determine if the form's field_data data contains an item.
	 *
	 * If the field doesn't exist in the field_data, false will be returned.
	 *
	 * @param  string  $field_name
	 * @return bool
	 */
	public static function has( $field_name )
	{
		
		return isset( static::$field_data[$field_name] ) && !empty( static::$field_data[$field_name] );

	}

	/**
	 * Get an item from the form's field data.
	 *
	 * <code>
	 *		// Get the "email" item from the form's field data array
	 *		$email = ExampleForm::get( 'email' );
	 *
	 *		// Return a default value if the specified item doesn't exist
	 *		$email = ExampleForm::get( 'email', 'not listed' );
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public static function get( $field_name, $default = null )
	{

		// prevent need to manually load input when populating forms

		if( empty( static::$field_data ) )
			static::unserialize_from_session();

		return static::has( $field_name ) ? static::$field_data[$field_name] : $default;

	}

	/**
	 * Get all of the persistent form data.
	 *
	 * @return array
	 */
	public static function all()
	{

		return static::$field_data;

	}

	/**
	 * Use to populate a form field. Loads the field's value from 
	 * flashed input data, if that's not present it loads the value
	 *
	 * <code>
	 *		// Get the "email" item from the form's field data array
	 *		$email = ExampleForm::get( 'email' );
	 *
	 *		// Return a default value if the specified item doesn't exist
	 *		$email = ExampleForm::get( 'email', 'not listed' );
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public static function populate( $field_name, $default = null )
	{

		// prevent need to manually load input when populating forms

		if( empty( static::$field_data ) )
			static::unserialize_from_session();

		// return input flash data, fallback on persistent for data, fallback on default

		return Input::old( $field_name, static::get( $field_name, $default ) );

	}

}