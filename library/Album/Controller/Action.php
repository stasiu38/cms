<?php
abstract class Album_Controller_Action extends Zend_Controller_Action {

	protected $obConfig;

	public function init() {
		// load configuration
		$this->obConfig = new Zend_Config_Ini('../application/configs/application.ini', 'general');

		set_include_path('.' . PATH_SEPARATOR . '../application/models/'
		. PATH_SEPARATOR . get_include_path());

		// setup database
		$db = Zend_Db::factory(
			$this->obConfig->db->adapter,
			$this->obConfig->db->config->toArray()   );
		Zend_Db_Table::setDefaultAdapter($db);
		$wtyczka = new Wtyczka();
		$this->view->wtyczka = $wtyczka->pobierzSzukaj(  ) ;
		var_dump($this->view->wtyczka[0]['online']);
		$a = $this->view->wtyczka[0]['online'];
		var_dump($a);
		$this->view->online = $a;
		$b = $this->view->wtyczka[0]['odnosnik'];
		$this->view->odnosnik = $b;
		$strony = new Strony();
		$this->view->strony = $strony->fetchAll();

		//initialize Zend Layout
		Zend_Layout::startMvc();
	}
}
?>