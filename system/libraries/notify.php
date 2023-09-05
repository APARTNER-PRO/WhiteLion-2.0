<?php if (!defined('SYS_PATH')) exit('Access denied');

class Notify {

	private $errors = [];
	private $success = [];
	private $warnings = [];
	private $infos = [];
	private $before;
	private $after;

	public function __construct($cfg)
	{
		$this->before = $cfg['before'] ?? '<p>';
		$this->after = $cfg['after'] ?? '</p>';

		foreach(['errors', 'success', 'warnings', 'infos'] as $key) {
			if(!empty($_SESSION['notify']->$key))
			 	$this->$key = $_SESSION['notify']->$key;
		}
		unset($_SESSION['notify']);
	}

	private function add($to, $text, $title, $group)
	{
		if(is_object($text))
			$this->$to[$group] = $text;
		else
		{
			$obj = new stdClass();
			$obj->text = '';
			if(!is_array($text))
				$obj->text = $text;
			else
				foreach($text as $t) {
					$obj->text .= $this->before . $t . $this->after;
				}
			$obj->title = $title;
			$obj->show_btn = false;
			$this->$to[$group][] = $obj;
		}
	}

	public function add_error($text, $title = 'Error!', $group = 'page')
	{
		$this->add('errors', $text, $title, $group);
	}

	public function add_success($text, $title = 'Success!', $group = 'page')
	{
		$this->add('success', $text, $title, $group);
	}

	public function add_warning($text, $title = 'Warning!', $group = 'page')
	{
		$this->add('warnings', $text, $title, $group);
	}

	public function add_info($text, $title = 'Info!', $group = 'page')
	{
		$this->add('infos', $text, $title, $group);
	}

	public function get($from, $group = 'page')
	{
		if(in_array($from, ['errors', 'success', 'warnings', 'infos']))
			return $this->$from[$group] ?? NULL;
		return NULL;
	}

	public function __before_after($before = NULL, $after = NULL)
	{
		if(is_null($before) && is_null($after))
			return ['before' => $this->before, 'after' => $this->after];

		if(!is_null($before))
			$this->before = $before;
		if(!is_null($after))
			$this->after = $after;
	}

	// use in loader::redirect()
	public function setToSession()
	{
		$_SESSION['notify'] = new stdClass();
		foreach(['errors', 'success', 'warnings', 'infos'] as $key) {
			$_SESSION['notify']->$key = $this->$key;
		}
	}

}