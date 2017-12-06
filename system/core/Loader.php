<?php
class FavCI_Loader
{
	protected $_favci_ob_level;

	// 视图层文件夹
	protected $_favci_view_path = [VIEWPATH];

	// 类库加载文件夹
	protected $_favci_library_path = [APPPATH, SYSPATH];

	// model加载文件夹
	protected $_favci_model_path = [APPPATH];

	// helper
	protected $_favci_helper_path = [APPPATH, SYSPATH];

	protected $_favci_cached_vars =	array();

	protected $_favci_classes =	array();

	protected $_favci_models =	array();

	protected $_favci_helpers =	array();

	public function __construct()
	{
		$this->_favci_ob_level = ob_get_level();
		$this->_favci_classes =& is_loaded();
	}

	// 自动加载autoload.php中预定义的类库
	public function initialize()
	{
		// $this->_favci_autoloader();
	}

	// protected function _favci_autoloader()
	// {

	// }

	// 检测类是否是_favci_classes中的方法
	public function is_loaded($class)
	{
		return array_search(ucfirst($class), $this->_favci_classes, TRUE);
	}

	// 加载视图
	public function view($view, $vars=[], $return=FALSE)
	{
		return $this->_favci_laod(array('_favci_view' => $view, '_favci_vars' => $this->_favci_prepare_view_vars($vars), '_favci_return' => $return));
	}

	protected function _favci_laod($data)
	{

	}

	protected function _favci_prepare_view_vars($vars)
	{
		if ( ! is_array($vars))
		{
			$vars = is_object($vars)
				? get_object_vars($vars)
				: array();
		}

		foreach (array_keys($vars) as $key)
		{
			if (strncmp($key, '_ci_', 4) === 0)
			{
				unset($vars[$key]);
			}
		}

		return $vars;
	}
}
