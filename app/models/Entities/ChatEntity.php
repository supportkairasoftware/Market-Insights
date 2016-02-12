<?php

class ChatEntity extends Eloquent{

    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	public $timestamps = false;
	public $table = 'chats';
	public $primaryKey = 'ChatID';
	public $incrementing = 'ChatID';
	//public $timestamps = false;
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	 
	public static $sendChat_rules = array(
        'FromUserID'=>'required',
        'ToUserID'=>'required',
    );
    
    public static $niceNameArray = array(
    	'Message' => 'Message',
    	'FromUserID' => 'From User',
    	'ToUserID' => 'To User',
    );
}
