<?php

class ExampleForm extends FormBase_Model
{

	public static $rules = array(
		'first_name' => 'required',
		'status'     => 'required',
	);

	public static $status = array(
		'' => 'Choose One',
		1  => 'Active',
		2  => 'inactive',
		3  => 'Deleted',
		4  => 'Boring',
		5  => 'Adventurer',
	);
}