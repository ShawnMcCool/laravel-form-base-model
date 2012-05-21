<h2>Review</h2>

<p>
	First Name: {{ ExampleForm::get( 'first_name', 'none entered' ) }}
</p>

<p>
	Last Name: {{ ExampleForm::get( 'last_name', 'none entered' ) }}
</p>

{{ HTML::link( 'form_example', 'Make Changes' ) }}