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

	// model加载
	public function model($model, $name = '', $db_conn = FALSE)
	{
		if (empty($model))
		{
			return $this;
		}
		elseif (is_array($model))
		{
			foreach ($model as $key => $value)
			{
				is_int($key) ? $this->model($value, '', $db_conn) : $this->model($key, $value, $db_conn);
			}

			return $this;
		}

		$path = '';

		// Is the model in a sub-folder? If so, parse out the filename and path.
		if (($last_slash = strrpos($model, '/')) !== FALSE)
		{
			// The path is in front of the last slash
			$path = substr($model, 0, ++$last_slash);

			// And the model name behind it
			$model = substr($model, $last_slash);
		}

		if (empty($name))
		{
			$name = $model;
		}

		if (in_array($name, $this->_ci_models, TRUE))
		{
			return $this;
		}

		$CI =& get_instance();
		if (isset($CI->$name))
		{
			throw new RuntimeException('The model name you are loading is the name of a resource that is already being used: '.$name);
		}

		if ($db_conn !== FALSE && ! class_exists('CI_DB', FALSE))
		{
			if ($db_conn === TRUE)
			{
				$db_conn = '';
			}

			$this->database($db_conn, FALSE, TRUE);
		}

		// Note: All of the code under this condition used to be just:
		//
		//       load_class('Model', 'core');
		//
		//       However, load_class() instantiates classes
		//       to cache them for later use and that prevents
		//       MY_Model from being an abstract class and is
		//       sub-optimal otherwise anyway.
		if ( ! class_exists('CI_Model', FALSE))
		{
			$app_path = APPPATH.'core'.DIRECTORY_SEPARATOR;
			if (file_exists($app_path.'Model.php'))
			{
				require_once($app_path.'Model.php');
				if ( ! class_exists('CI_Model', FALSE))
				{
					throw new RuntimeException($app_path."Model.php exists, but doesn't declare class CI_Model");
				}
			}
			elseif ( ! class_exists('CI_Model', FALSE))
			{
				require_once(BASEPATH.'core'.DIRECTORY_SEPARATOR.'Model.php');
			}

			$class = config_item('subclass_prefix').'Model';
			if (file_exists($app_path.$class.'.php'))
			{
				require_once($app_path.$class.'.php');
				if ( ! class_exists($class, FALSE))
				{
					throw new RuntimeException($app_path.$class.".php exists, but doesn't declare class ".$class);
				}
			}
		}

		$model = ucfirst($model);
		if ( ! class_exists($model, FALSE))
		{
			foreach ($this->_ci_model_paths as $mod_path)
			{
				if ( ! file_exists($mod_path.'models/'.$path.$model.'.php'))
				{
					continue;
				}

				require_once($mod_path.'models/'.$path.$model.'.php');
				if ( ! class_exists($model, FALSE))
				{
					throw new RuntimeException($mod_path."models/".$path.$model.".php exists, but doesn't declare class ".$model);
				}

				break;
			}

			if ( ! class_exists($model, FALSE))
			{
				throw new RuntimeException('Unable to locate the model you have specified: '.$model);
			}
		}
		elseif ( ! is_subclass_of($model, 'CI_Model'))
		{
			throw new RuntimeException("Class ".$model." already exists and doesn't extend CI_Model");
		}

		$this->_ci_models[] = $name;
		$CI->$name = new $model();
		return $this;
	}
}
