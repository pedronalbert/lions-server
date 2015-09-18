<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DateTime;

class Backup extends Model {

	public function getDateTimeString() {
		$date = new DateTime($this->created_at);

		return $date->format('Y_m_d_His');
	}

	public function setPrettyName() {
		$this->name = $this->getDateTimeString().'.sql';
	}
}
