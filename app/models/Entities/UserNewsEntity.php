<?php

class UserNewsEntity extends Eloquent{

    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	public $timestamps = false;
	public $table = 'usernews';
	public $primaryKey = 'UserNewsID';
	public $incrementing = 'UserNewsID';
	//public $timestamps = false;
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
}
