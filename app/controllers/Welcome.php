<?php
class Welcome extends FavCI_Controller
{
	function __construct()
	{
		parent::__construct();
	}

	public function index(){
		$this->load->view('welcome/index',array('a'=>'hello word'));
	}
}
