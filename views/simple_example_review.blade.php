<h2>Review</h2>

<p>
	First Name: {{ ExampleForm::get( 'first_name', 'none entered' ) }}
</p>

<p>
	Last Name: {{ ExampleForm::get( 'last_name', 'none entered' ) }}
</p>

<p>
	Status: {{ ExampleForm::get( 'status', 'none entered' ) }}
</p>


{{ HTML::link_to_route( 'form_examples', 'Make Changes', array( 'simple_example' ) ) }} {{ HTML::link_to_route( 'form_examples', 'Return to Examples Page', array( 'index' ) ) }}