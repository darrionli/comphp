<?php
class FavCI_Controller
{
	// 单例
	private static $instance;

	public function __construct()
	{
		self::$instance =& $this;

		// 分配所有被实例化的对象
		foreach (is_loaded() as $var => $class) {
			var_dump($class);
			$this->$var =& load_class($class);
		}

		// $this->load =& load_class('Loader', 'core');
		// $this->load->initialize();
	}

	public static function &get_instance()
	{
		return self::$instance;
	}
}
