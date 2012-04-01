<?php
	/**
	 * This interface represents a database-driver
	 */
	interface CI_Database {
		public function query($sql);
		public function insert($table, $data);
		public function update($table, $data, $options);
		public function delete($table, $data);
		public function truncate($table);
		public function insertId();
		public function numRows($query);
	}
?>