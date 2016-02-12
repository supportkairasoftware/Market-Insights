<?php


class SegmentEntity extends Eloquent{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    public $timestamps = false;
    public $table = 'lu_segments';
    public $primaryKey = 'SegmentID';
    public $incrementing = 'SegmentID';
    //public $timestamps = false;
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    public $Model_Types = array(
        'SegmentID'=>'int',
        'SegmentName'=>'string',
        'IsEnabled'=>'int'
    );
}
