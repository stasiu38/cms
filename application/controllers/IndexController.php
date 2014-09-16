<?php

class IndexController extends Album_Controller_Action
{
function init() {
	parent::init();
	$this->view->baseUrl = $this->_request->getBaseUrl();
	Zend_Loader::loadClass('Pages');

	$wtyczka = new Wtyczka();
	$this->view->wtyczka = $wtyczka->pobierzSzukaj(  ) ;

	$a = $this->view->wtyczka[0]['online'];

	$this->view->online = $a;
	$b = $this->view->wtyczka[0]['odnosnik'];
	$this->view->odnosnik = $b;

	  
   }

    function indexAction() {
	  $this->view->baseUrl = $this->_request->getBaseUrl();
	  $this->view->title = "Wirtulna Biblioteka";
		$pages = new Pages();
		$this->view->pages =$pages->fetchAll();
		$strony = new Strony();
		$this->view->strony = $strony->fetchAll();
		$wtyczka = new Wtyczka();
		$this->view->wtyczka = $wtyczka->fetchAll();



   }

	function szczegolyAction(){
		$oRequest = $this->getRequest();
		$iPages = $oRequest->getParam( 'id' );

		$oPages = new Pages();
		$this->view->oPages = $oPages->find( $iPages )->current();
		$strony = new Strony();
		$this->view->strony = $strony->fetchAll();

	}
	function stronaAction(){
		$oRequest = $this->getRequest();
		$iStrony = $oRequest->getParam( 'id' );
		$strony = new Strony();
		$this->view->strony = $strony->fetchAll();

		$oStrony = new Strony();
		$this->view->oStrony = $oStrony->find( $iStrony )->current();

	}





}


   