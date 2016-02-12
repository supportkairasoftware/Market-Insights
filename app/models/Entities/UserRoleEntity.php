<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class UserRoleEntity extends Eloquent implements UserInterface,   RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	public $timestamps = false;
	public $table = 'userroles';
	public $primaryKey = 'UserRoleID';
	public $incrementing = 'UserRoleID';
	//public $timestamps = false;
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */


	protected $hidden = array('Password', 'Remember_Token');

    public $Model_Types = array(
        'UserRoleID'=>'string',
        'UserID'=>'int',
        'Password'=>'int'
    );
}
