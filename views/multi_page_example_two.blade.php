{{ Form::open() }}

	<p>
		Street Address: {{ Form::text( 'street_address', ExampleForm::old( 'street_address' ) ) }}
	</p>

	<p>
		Suite / Apt #: {{ Form::text( 'suite_number', ExampleForm::old( 'suite_number' ) ) }}
	</p>

	<p>
		Favorite Foods:

		@foreach( ExampleForm::$foods as $food_id => $food_name )
			{{ Form::checkbox( 'favorite_foods[]', $food_id, in_array( $food_id, (array) ExampleForm::old( 'favorite_foods' ) ) ) }} {{ $food_name }}
		@endforeach
	</p>

	{{ Form::submit( 'Next' ) }}
	{{ HTML::link_to_route( 'form_examples', 'Previous', array( 'multi_page_example_one' ) ) }}<br />
{{ Form::close() }}