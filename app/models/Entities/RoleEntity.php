<?php


class RoleEntity extends Eloquent{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	public $timestamps = false;
	public $table = 'lu_roles';
	public $primaryKey = 'RoleID';
	public $incrementing = 'RoleID';
	//public $timestamps = false;
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */

    public $Model_Types = array(
        'RoleID'=>'int',
        'RoleName'=>'string'
    );
}
