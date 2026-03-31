<?php

defined('BASEPATH') OR exit('No direct script access allowed');

interface CI_Session_driver_interface {
	public function open($save_path, $name);
	public function close();
	public function read($session_id);
	public function write($session_id, $session_data);
	public function destroy($session_id);
	public function gc($maxlifetime);
	public function updateTimestamp($session_id, $data);
	public function validateId($session_id);
}