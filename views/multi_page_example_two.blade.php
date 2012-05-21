{{ Form::open() }}

	<p>
		Street Address: {{ Form::text( 'street_address', Input::old( 'street_address' ) ) }}
	</p>

	<p>
		Suite / Apt #: {{ Form::text( 'suite_number', Input::old( 'suite_number' ) ) }}
	</p>

	<p>
		Favorite Foods:

		@foreach( ExampleForm::$foods as $food_id => $food_name )
			{{ Form::checkbox( 'favorite_foods[]', $food_id, in_array( $food_id, (array) Input::old( 'favorite_foods' ) ) ) }} {{ $food_name }}
		@endforeach
	</p>

	{{ Form::submit( 'Review' ) }}
	{{ HTML::link( 'form_example', 'Previous' ) }}
{{ Form::close() }}