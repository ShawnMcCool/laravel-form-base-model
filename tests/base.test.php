<?php namespace FormBaseModel;

\Bundle::start('form-base-model');

class TestForm extends Base
{

}

class TestModel extends \Eloquent
{

}

class Test_Base extends \PHPUnit_Framework_TestCase
{

	public $test_data = array(
		'robots' => 'awesome',
		'cats'   => 'allergies',
	);

	public function setup()
	{

		\Laravel\Session::load();

	}

	public function teardown()
	{

		TestForm::reset();

		\Input::clear();

	}

	public function test_can_reset_form()
	{

		TestForm::$field_data               = true;
		TestForm::$rules                    = true;
		TestForm::$messages                 = true;
		TestForm::$validation               = true;
		TestForm::$loaded                   = true;
		TestForm::$custom_validators_loaded = true;

		TestForm::reset();

		$this->assertEquals( null   , TestForm::$field_data );
		$this->assertEquals( array(), TestForm::$rules );
		$this->assertEquals( array(), TestForm::$messages );
		$this->assertEquals( false  , TestForm::$validation );
		$this->assertEquals( null   , TestForm::$loaded );
		$this->assertEquals( null   , TestForm::$custom_validators_loaded );

	}

	public function test_can_load_array()
	{

		TestForm::load( $this->test_data );

		$this->assertEquals( 'awesome', TestForm::get( 'robots' ));
		$this->assertEquals( 'allergies', TestForm::get( 'cats' ));

	}

	public function test_can_load_eloquent_model()
	{

		$data = new TestModel( $this->test_data );

		TestForm::load( $data );

		$this->assertEquals( 'awesome', TestForm::get( 'robots' ));
		$this->assertEquals( 'allergies', TestForm::get( 'cats' ));

	}

	public function test_loaded_object_detection()
	{

		$this->assertEquals( false, TestForm::loaded() );

		TestForm::load( $this->test_data );

		$this->assertEquals( true, TestForm::loaded() );

	}

	public function test_can_get_default_value()
	{

		$this->assertEquals( 'default value', TestForm::get( 'non_existent key', 'default value' ) );

	}

	public function test_can_get_single_value()
	{

		Testform::load( $this->test_data );

		$this->assertEquals( 'awesome', TestForm::get( 'robots' ));
		$this->assertEquals( 'allergies', TestForm::get( 'cats' ));

	}

	public function test_can_get_multiple_values()
	{

		Testform::load( $this->test_data );

		$this->assertEquals( $this->test_data, TestForm::get( array( 'robots', 'cats' )));

	}

	public function test_can_get_all()
	{

		Testform::load( $this->test_data );

		$this->assertEquals( $this->test_data, TestForm::all());

	}

	public function test_all_default()
	{

		$this->assertEquals( array(), TestForm::all() );

	}

	public function test_that_validation_with_no_rules_passes()
	{

		$this->assertTrue( TestForm::is_valid() );

	}

	public function test_simple_validation_fails()
	{

		TestForm::$rules = array(
			'non_existent' => 'required',
		);

		$this->assertFalse( TestForm::is_valid() );

	}

	public function test_validation_input_from_parameter()
	{

		TestForm::$rules = array(
			'non_existent' => 'required',
		);

		$this->assertFalse( TestForm::is_valid( null, $this->test_data ));

		TestForm::$rules = array(
			'robots' => 'required',
		);

		$this->assertTrue( TestForm::is_valid( null, $this->test_data ));

	}

	public function test_validation_field_exclusion()
	{

		TestForm::$rules = array(
			'non_existent' => 'required',
			'robots'       => 'required',
		);

		$this->assertFalse( TestForm::is_valid( array( 'non_existent' ), $this->test_data ));

		$this->assertTrue( TestForm::is_valid( array( 'robots' ), $this->test_data ));

	}

	public function test_validation_from_input_class()
	{

		\Input::replace( $this->test_data );

		TestForm::$rules = array(
			'non_existent' => 'required',
			'robots'       => 'required',
		);

		$this->assertFalse( TestForm::is_valid( array( 'non_existent' )));

		$this->assertTrue( TestForm::is_valid( array( 'robots' )));

	}

	public function test_save_input()
	{

		TestForm::load( $this->test_data );

		TestForm::save_input();

		TestForm::reset();

		$this->assertEquals( $this->test_data, TestForm::all() );

	}

	public function test_has_value_check()
	{

		$this->assertFalse( TestForm::has( 'robots' ));

		TestForm::load( $this->test_data );

		$this->assertTrue( TestForm::has( 'robots' ));

	}

	public function test_set_values()
	{

		TestForm::set( 'cows', 'moo' );

		$this->assertEquals( 'moo', TestForm::get( 'cows' ));

	}

	public function test_old_returns_existing_value()
	{

		TestForm::load( $this->test_data );

		$this->assertEquals( 'awesome', TestForm::old( 'robots' ));

	}

	public function test_old_returns_existing_values()
	{

		TestForm::load( $this->test_data );

		$this->assertEquals( $this->test_data, TestForm::old( array( 'robots', 'cats' )));

	}

	public function test_old_returns_default_value()
	{

		$this->assertEquals( null, TestForm::old( 'robots' ));

		$this->assertEquals( 'default value', TestForm::old( 'robots', 'default value' ));

		TestForm::load( $this->test_data );

		$this->assertEquals( 'default value', TestForm::old( 'non_existent', 'default value' ));

	}

	public function test_old_returns_default_values()
	{

		$this->assertEquals( array( 'ne' => null, 'ne2' => null, 'ne3' => null ), TestForm::old( array( 'ne', 'ne2', 'ne3' ), null ));

		TestForm::load( $this->test_data );

		$this->assertEquals( array( 'ne1' => 'ne', 'ne2' => 'ne', 'ne3' => 'ne' ), TestForm::old( array( 'ne1', 'ne2', 'ne3' ), 'ne' ));

	}

	public function test_old_returns_input_flash()
	{

		TestForm::load( $this->test_data );

		\Session::$instance->session['data']['laravel_old_input'] = array( 'robots' => 'not so cool' );

		$this->assertEquals( 'not so cool', TestForm::old( 'robots' ));

	}

	public function test_old_returns_input_flash_multiple()
	{

		TestForm::load( $this->test_data );

		\Session::$instance->session['data']['laravel_old_input'] = array( 'robots' => 'not so cool', 'cats' => 'who cares' );

		$this->assertEquals( array( 'robots' => 'not so cool', 'cats' => 'who cares' ), TestForm::old( array( 'robots', 'cats' )));

	}

	public function test_old_checkbox_matches()
	{

		\Session::$instance->session['data']['laravel_old_input'] = array( 'active' => 1 );

		$this->assertTrue( TestForm::old_checkbox( 'active', 1, true ));

	}

	public function test_old_checkbox_doesnt_match()
	{

		\Session::$instance->session['data']['laravel_old_input'] = array( 'active' => 1 );

		$this->assertFalse( TestForm::old_checkbox( 'active', 0 ));

	}

	public function test_old_checkbox_default()
	{

		\Session::$instance->session['data']['laravel_old_input'] = array( 'active' => 1 );

		$this->assertEquals( 'happy', TestForm::old_checkbox( 'awesome', 0, 'happy' ));

		$this->assertEquals( 'happy', TestForm::old_checkbox( 'active', 0, 'happy' ));

		$this->assertTrue( TestForm::old_checkbox( 'active', 1, false ));

	}

}