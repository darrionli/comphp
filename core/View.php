<?php
namespace core;
class View
{
	// 模板文件
	protected $file = null;

	// 模板变量
	protected $vars = [];

	// 加载模板文件
	public function make ($file)
	{
		$this->file = 'view/' . $file . '.html';
		return $this;
	}

	// 分配变量
	public function with($name, $value)
	{
		$this->vars[$name] = $value;
		return $this;
	}

	public function __toString()
	{
		extract($this->vars);
		include $this->file;
		return '';
	}
}
