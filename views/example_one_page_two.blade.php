{{ Form::open() }}

	<p>
		Street Address: {{ Form::text( 'street_address', Input::old( 'street_address' ) ) }}
	</p>

	<p>
		Suite / Apt #: {{ Form::text( 'suite_number', Input::old( 'suite_number' ) ) }}
	</p>

	{{ Form::submit( 'Review' ) }}
	{{ HTML::link( 'form_example', 'Previous' ) }}
{{ Form::close() }}