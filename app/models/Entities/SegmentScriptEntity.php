<?php


class SegmentScriptEntity extends Eloquent{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    public $timestamps = false;
    public $table = 'segmentscripts';
    public $primaryKey = 'SegmentScriptID';
    public $incrementing = 'SegmentScriptID';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    public $Model_Types = array(
        'SegmentScriptID'=>'int',
        'Script'=>'string'
    );
}
