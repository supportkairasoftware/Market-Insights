<?php

class vwUserRoleEntity extends Eloquent{

       /**
     * The database table used by the model.
     *
     * @var string
     */
    public $timestamps = false;
    public $table = 'vw_userroles';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    public $Model_Types = array(
		'UserRoleID'=>'int',
		'UserID'=>'int',
        'RoleID'=>'int',
        'RoleName'=>'string'
    );

}
