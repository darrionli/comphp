<?php
class FavCI_Loader
{
	protected $_favci_ob_level;

	// 视图层文件夹
	protected $_favci_view_paths = [VIEWPATH=>TRUE];

	// 类库加载文件夹
	protected $_favci_library_paths = [APPPATH, SYSPATH];

	// model加载文件夹
	protected $_favci_model_paths = [APPPATH];

	// helper
	protected $_favci_helper_paths = [APPPATH, SYSPATH];

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
		return $this->_favci_load(array('_ci_view' => $view, '_ci_vars' => $this->_favci_prepare_view_vars($vars), '_ci_return' => $return));
	}

	protected function _favci_load($_ci_data)
	{
		foreach (array('_ci_view', '_ci_vars', '_ci_path', '_ci_return') as $_ci_val)
		{
			$$_ci_val = isset($_ci_data[$_ci_val]) ? $_ci_data[$_ci_val] : FALSE;
		}
		$file_exists = FALSE;

		// Set the path to the requested file
		if (is_string($_ci_path) && $_ci_path !== '')
		{
			$_ci_x = explode('/', $_ci_path);
			$_ci_file = end($_ci_x);
		}
		else
		{
			$_ci_ext = pathinfo($_ci_view, PATHINFO_EXTENSION);
			$_ci_file = ($_ci_ext === '') ? $_ci_view.'.php' : $_ci_view;

			foreach ($this->_favci_view_paths as $_ci_view_file => $cascade)
			{
				if (file_exists($_ci_view_file.$_ci_file))
				{
					$_ci_path = $_ci_view_file.$_ci_file;
					$file_exists = TRUE;
					break;
				}

				if ( ! $cascade)
				{
					break;
				}
			}
		}

		if ( ! $file_exists && ! file_exists($_ci_path))
		{
			exit('Unable to load the requested file: '.$_ci_file);
		}

		// This allows anything loaded using $this->load (views, files, etc.)
		// to become accessible from within the Controller and Model functions.
		$_ci_CI =& get_instance();
		foreach (get_object_vars($_ci_CI) as $_ci_key => $_ci_var)
		{
			if ( ! isset($this->$_ci_key))
			{
				$this->$_ci_key =& $_ci_CI->$_ci_key;
			}
		}

		/*
		 * Extract and cache variables
		 *
		 * You can either set variables using the dedicated $this->load->vars()
		 * function or via the second parameter of this function. We'll merge
		 * the two types and cache them so that views that are embedded within
		 * other views can have access to these variables.
		 */
		empty($_ci_vars) OR $this->_favci_cached_vars = array_merge($this->_favci_cached_vars, $_ci_vars);
		extract($this->_favci_cached_vars);

		/*
		 * Buffer the output
		 *
		 * We buffer the output for two reasons:
		 * 1. Speed. You get a significant speed boost.
		 * 2. So that the final rendered template can be post-processed by
		 *	the output class. Why do we need post processing? For one thing,
		 *	in order to show the elapsed page load time. Unless we can
		 *	intercept the content right before it's sent to the browser and
		 *	then stop the timer it won't be accurate.
		 */
		ob_start();

		// If the PHP installation does not support short tags we'll
		// do a little string replacement, changing the short tags
		// to standard PHP echo statements.
		if ( ! is_php('5.4') && ! ini_get('short_open_tag'))
		{
			echo eval('?>'.preg_replace('/;*\s*\?>/', '; ?>', str_replace('<?=', '<?php echo ', file_get_contents($_ci_path))));
		}
		else
		{
			include($_ci_path); // include() vs include_once() allows for multiple views with the same name
		}


		// Return the file data if requested
		if ($_ci_return === TRUE)
		{
			$buffer = ob_get_contents();
			@ob_end_clean();
			return $buffer;
		}

		/*
		 * Flush the buffer... or buff the flusher?
		 *
		 * In order to permit views to be nested within
		 * other views, we need to flush the content back out whenever
		 * we are beyond the first level of output buffering so that
		 * it can be seen and included properly by the first included
		 * template and any subsequent ones. Oy!
		 */
		if (ob_get_level() > $this->_favci_ob_level + 1)
		{
			ob_end_flush();
		}
		else
		{
			$_ci_CI->output->append_output(ob_get_contents());
			@ob_end_clean();
		}

		return $this;
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
