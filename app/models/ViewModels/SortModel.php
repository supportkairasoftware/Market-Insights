<?php
namespace ViewModels;

class SortModel
{
    public $Index;
    public $Direction;
    public function __construct($Index,$Direction){
        $this->Index = $Index;
        $this->Direction = $Direction;
    }
}


