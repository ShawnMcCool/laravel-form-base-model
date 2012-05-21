{{ Form::open() }}

	<p>
		First Name: {{ Form::text( 'first_name', Input::old( 'first_name' ) ) }}
		{{ $errors->has( 'first_name' ) ? $errors->first( 'first_name' ) : '' }}
	</p>

	<p>
		Last Name: {{ Form::text( 'last_name', Input::old( 'last_name' ) ) }}
		{{ $errors->has( 'last_name' ) ? $errors->first( 'last_name' ) : '' }}
	</p>

	<p>
		Status: {{ Form::select( 'status', ExampleForm::$status, Input::old( 'status' ) ) }}
		{{ $errors->has( 'status' ) ? $errors->first( 'status' ) : '' }}
	</p>

	{{ Form::submit( 'Next' ) }}
	
{{ Form::close() }}