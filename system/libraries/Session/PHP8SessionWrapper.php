<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class CI_SessionWrapper implements SessionHandlerInterface, SessionUpdateTimestampHandlerInterface {
	protected CI_Session_driver_interface $driver;
	public function __construct(CI_Session_driver_interface $driver)
	{
		$this->driver = $driver;
	}

	public function open(string $save_path, string $name): bool
	{
		return $this->driver->open($save_path, $name);
	}

	public function close(): bool
	{
		return $this->driver->close();
	}

	public function read(string $id): string|false
	{
		$data = $this->driver->read($id);
		if ($data === FALSE)
		{
			return FALSE;
		}
		return is_string($data) ? $data : '';
	}

	public function write(string $id, string $data): bool
	{
		return $this->driver->write($id, $data);
	}

	public function destroy(string $id): bool
	{
		return $this->driver->destroy($id);
	}

	public function gc(int $maxlifetime): int|false
	{
		$result = $this->driver->gc($maxlifetime);
		if ($result === FALSE)
		{
			return FALSE;
		}
		return is_int($result) ? $result : 0;
	}

	public function updateTimestamp(string $id, string $data): bool
	{
		return $this->driver->updateTimestamp($id, $data);
	}

	public function validateId(string $id): bool
	{
		return $this->driver->validateId($id);
	}
}