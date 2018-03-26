<?php
namespace app\controller;
use core\View;

class Index
{
	protected $view;

	public function __construct()
	{
		$this->view = new View();
	}

	public function index()
	{
		return $this->view->make('index')->with('name','lidi');
	}
}
