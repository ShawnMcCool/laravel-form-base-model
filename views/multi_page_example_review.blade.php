<h2>Review</h2>

<p>
	First Name: {{ ExampleForm::get( 'first_name', 'none entered' ) }}
</p>

<p>
	Last Name: {{ ExampleForm::get( 'last_name', 'none entered' ) }}
</p>

<p>
	Street Address: {{ ExampleForm::get( 'street_address', 'none entered' ) }}
</p>

@if( ExampleForm::has( 'suite_number' ) )
	<p>
		Suite / Apt #: {{ ExampleForm::get( 'suite_number' ) }}
	</p>
@endif

<p>
	Favorite Foods:
	
	<? $foods = Input::old( 'favorite_foods' ); // workaround for blade forelse bug that i only just now found ?>

	@forelse( $foods as $food_id )
		{{ ExampleForm::$foods[$food_id] }}<br />
	@empty
		No foods selected.
	@endforelse

</p>

{{ HTML::link_to_route( 'form_examples', 'Make Changes', array( 'multi_page_example_one' ) ) }} {{ HTML::link_to_route( 'form_examples', 'Clear and Start Over', array( 'clear_data' ) ) }}