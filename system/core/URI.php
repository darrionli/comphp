<?php
class FavCI_URI
{
	// URI字符串
	public $uri_string = '';

	// URI列表
	public $segments = array();

	// URI段允许的字符
	protected $_permitted_uri_chars = 'a-z 0-9~%.:_\-i@';

	// URI路由列表
	public $rsegments = array();

	public function __construct()
	{
		$uri = $this->_parse_request_uri();
		$this->_set_uri_string($uri);
	}

	/**
	 * 解析REQUEST_URI并自动检测URI
	 * 必要时修复查询字符串
	 */
	protected function _parse_request_uri()
	{
		if ( ! isset($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME']))
		{
			return '';
		}

		$uri = parse_url('http://dummy'.$_SERVER['REQUEST_URI']);
		$query = isset($uri['query']) ? $uri['query'] : '';
		$uri = isset($uri['path']) ? $uri['path'] : '';
		if (isset($_SERVER['SCRIPT_NAME'][0]))
		{
			if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0)
			{
				$uri = (string) substr($uri, strlen($_SERVER['SCRIPT_NAME']));
			}
			elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0)
			{
				$uri = (string) substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
			}
		}

		if (trim($uri, '/') === '' && strncmp($query, '/', 1) === 0)
		{
			$query = explode('?', $query, 2);
			$uri = $query[0];
			$_SERVER['QUERY_STRING'] = isset($query[1]) ? $query[1] : '';
		}
		else
		{
			$_SERVER['QUERY_STRING'] = $query;
		}

		parse_str($_SERVER['QUERY_STRING'], $_GET);

		if ($uri === '/' OR $uri === '')
		{
			return '/';
		}
		return $this->_remove_relative_directory($uri);
	}

	// 去除目录符号
	protected function _remove_relative_directory($uri)
	{
		$uris = array();
		$tok = strtok($uri, '/');
		while ($tok !== FALSE)
		{
			if (( ! empty($tok) OR $tok === '0') && $tok !== '..')
			{
				$uris[] = $tok;
			}
			$tok = strtok('/');
		}
		return implode('/', $uris);
	}

	// 设置uri字符串
	protected function _set_uri_string($str)
	{
		// 过滤掉特殊字符
		$this->uri_string = trim(remove_invisible_characters($str, FALSE), '/');

		if ($this->uri_string !== '')
		{
			$this->segments[0] = NULL;
			foreach (explode('/', trim($this->uri_string, '/')) as $val)
			{
				$val = trim($val);
				$this->filter_uri($val);

				if ($val !== '')
				{
					$this->segments[] = $val;
				}
			}

			unset($this->segments[0]);
		}
	}

	public function filter_uri(&$str)
	{
		if ( ! empty($str) && ! empty($this->_permitted_uri_chars) && ! preg_match('/^['.$this->_permitted_uri_chars.']+$/i'.(UTF8_ENABLED ? 'u' : ''), $str))
		{
			exit('The URI you submitted has disallowed characters.');
			show_error('The URI you submitted has disallowed characters.', 400);
		}
	}
}
