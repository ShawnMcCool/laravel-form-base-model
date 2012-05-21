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

	public static $foods = array(
		1 => 'Tacos',
		2 => 'Chicken Tacos',
		3 => 'Beef Tacos',
		4 => 'Vegetarian Tacos',
		5 => 'Black Bean Nachos',
	);

}