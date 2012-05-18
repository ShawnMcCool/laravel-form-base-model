<h2>Review</h2>

@if( ExampleForm::has( 'first_name' ) )
	<p>
		First Name: {{ ExampleForm::get( 'first_name' ) }}
	</p>
@endif

@if( ExampleForm::has( 'last_name' ) )
	<p>
		Last Name: {{ ExampleForm::get( 'last_name' ) }}
	</p>
@endif