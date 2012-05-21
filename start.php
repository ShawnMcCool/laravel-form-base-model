<?php

// register the class

Autoloader::map(array(
    'FormBase_Model' => __DIR__.'/formbase_model.php',
));

// register the example code
// you may remove or comment out the following lines to disable example routing

Autoloader::map(array(
	'ExampleForm' => __DIR__.'/models/exampleform.php',
));

Route::any( 'form_examples/(:any?)/(:any?)/(:any?)/(:any?)/(:any?)', array(
	'as'       => 'form_examples',
	'uses'     => 'form-base-model::examples@(:1)',
	'defaults' => array( 'index' ),
));
