<?php


class vwCallListEntity extends Eloquent  {

    public $timestamps = false;
    public $table = 'vw_calllist';
    public $primaryKey = 'CallID';
    public $incrementing = 'CallID';

    public $Model_Types = array(
        'CallID'=>'int',
        'IsOpen'=>'int',
        'SegmentScriptID'=>'int',
        'ResultID'=>'int',
        'SegmentID'=>'int',
        'Action'=>'string',
        'CreatedDate'=>'string',
        'SegmentName'=>'string',
        'Script'=>'string'
    );
}
