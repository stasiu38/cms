<?php

class Wtyczka extends Zend_Db_Table {
	protected $_name = 'wtyczka';
	protected $_id = 'wt_id';


	function pobierzGalerie(){

		$zapytanie = "select * from wtyczka WHERE wt_id=1";

		$db = $this->getAdapter();
		$zapytanie = $db->query( $zapytanie );

		return $zapytanie->fetchAll();
	}
	function pobierzSzukaj(){

		$zapytanie = "select * from wtyczka WHERE wt_id=2";

		$db = $this->getAdapter();
		$zapytanie = $db->query( $zapytanie );

		return $zapytanie->fetchAll();
	}
}