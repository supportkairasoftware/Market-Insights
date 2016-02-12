<?php


class ResultEntity extends Eloquent{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    public $timestamps = false;
    public $table = 'results';
    public $primaryKey = 'ResultID';
    public $incrementing = 'ResultID';
    //public $timestamps = false;
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    public $Model_Types = array(
        'ResultID'=>'int',
        'ResultName'=>'string'
    );
}
