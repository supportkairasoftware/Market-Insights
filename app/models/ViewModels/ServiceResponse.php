<?php
namespace ViewModels;

class ServiceResponse {
	public $IsSuccess;
	public $Data;
	public $Message;
	public $ErrorCode;
    public $Token;
	//public $ErrorMessages;
	
	public function __construct($IsSuccess = false){
		$this->IsSuccess = $IsSuccess;
        $this->Message = '';
        $this->Data = '';
		$this->ErrorCode = 0;
        $this->Token = '';
	}
}
