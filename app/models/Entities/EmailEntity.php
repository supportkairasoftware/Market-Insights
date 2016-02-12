<?php

class EmailEntity extends Eloquent{

    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	public $timestamps = false;
	public $table = 'emails';
	public $primaryKey = 'EmailID';
	public $incrementing = 'EmailID';
	//public $timestamps = false;
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
}
