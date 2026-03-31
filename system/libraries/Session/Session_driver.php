<?php

defined('BASEPATH') OR exit('No direct script access allowed');

abstract class CI_Session_driver {
	protected $_config;
	protected $_fingerprint;
	protected $_lock = FALSE;
	protected $_session_id;
	protected $_success, $_failure;
	public function __construct(&$params)
	{
		$this->_config =& $params;
		if (is_php('7'))
		{
			$this->_success = TRUE;
			$this->_failure = FALSE;
		}
		else
		{
			$this->_success = 0;
			$this->_failure = -1;
		}
	}

	public function php5_validate_id()
	{
		if ($this->_success === 0 && isset($_COOKIE[$this->_config['cookie_name']]) && ! $this->validateId($_COOKIE[$this->_config['cookie_name']]))
		{
			unset($_COOKIE[$this->_config['cookie_name']]);
		}
	}

	protected function _cookie_destroy()
	{
		if ( ! is_php('7.3'))
		{
			$header = 'Set-Cookie: '.$this->_config['cookie_name'].'=';
			$header .= '; Expires='.gmdate('D, d-M-Y H:i:s T', 1).'; Max-Age=-1';
			$header .= '; Path='.$this->_config['cookie_path'];
			$header .= ($this->_config['cookie_domain'] !== '' ? '; Domain='.$this->_config['cookie_domain'] : '');
			$header .= ($this->_config['cookie_secure'] ? '; Secure' : '').'; HttpOnly; SameSite='.$this->_config['cookie_samesite'];
			header($header);
			return;
		}
		return setcookie(
			$this->_config['cookie_name'], '',
			array(
				'expires' => 1,
				'path' => $this->_config['cookie_path'],
				'domain' => $this->_config['cookie_domain'],
				'secure' => $this->_config['cookie_secure'],
				'httponly' => TRUE,
				'samesite' => $this->_config['cookie_samesite']
			)
		);
	}

	protected function _get_lock($session_id)
	{
		$this->_lock = TRUE;
		return TRUE;
	}

	protected function _release_lock()
	{
		if ($this->_lock)
		{
			$this->_lock = FALSE;
		}
		return TRUE;
	}
}