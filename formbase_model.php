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
	public static function is_valid( $fields, $input = null )
	{

		if( !is_array( $fields ) )
			return false;

		if( is_null( $input ) )
			$input = Input::all();

		$field_rules = array();

		foreach( $fields as $field_name )
			if( array_key_exists( $field_name, static::$rules ) )
				$field_rules[$field_name] = static::$rules[$field_name];

		if( empty( $field_rules) )
			return true;

        static::$validation = Validator::make( $input, $field_rules );

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
	 * Retrieves input and stores it in the model's field_data array.
	 * Only the fields declared in the $field array will be stored.
	 *
	 * <code>
	 *		// Store form input data
	 *		ExampleForm::store_input( array( 'first_name', 'last_name' ) );
	 * </code>
	 *
	 * @param  array   $fields
	 * @param  array   $input
	 */
	public static function store_input( $fields, $input = null )
	{

		if( !is_array( $fields ) )
			return false;

		if( is_null( $input ) )
			$input = Input::all();

		if( Input::old() && is_null( static::$input ) )
			static::load_input();

		foreach( $fields as $field_name )
		{
			if( Input::has( $field_name ) )
				static::$field_data[$field_name] = Input::get( $field_name );
			else
				static::$field_data[$field_name] = '';

		}

		static::serialize_to_session();

	}

	/**
	 * Loads input from session and detects if the form was redirected
	 * back to the same page so that it can repopulate Input::old()
	 * fields from the field_data array.
	 *
	 * <code>
	 *		// prepare form population
	 *		ExampleForm::load_input();
	 * </code>
	 */
	public static function load_input()
	{

		static::unserialize_from_session();

		if( Input::old() )
			Session::forget( Input::old_input );
		else
			Session::put( Input::old_input, static::$field_data );

		static::$input = (object) static::$field_data;

	}

	/**
	 * Empty persistent form field_data.
	 */
	protected function reset_input()
	{

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
		
		return isset( static::$field_data[$field_name] );

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

		return static::has( $field_name ) ? static::$field_data[$field_name] : $default;

	}
}