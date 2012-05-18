{{ Form::open() }}

	<p>
		First Name: {{ Form::text( 'first_name', Input::old( 'first_name' ) ) }}
	</p>

	<p>
		Last Name: {{ Form::text( 'last_name', Input::old( 'last_name' ) ) }}
	</p>

	{{ Form::submit( 'Save' ) }}
	
{{ Form::close() }}