<?php
class ProduktyController extends Album_Controller_Action
{
	function init() {
		parent::init();
		$this->_helper->layout->setLayout( 'shoplayout' );
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Produkty');
		Zend_Loader::loadClass('Kategorie');
		Zend_Loader::loadClass('Wtyczka');

		$wtyczka = new Wtyczka();
		$this->view->wtyczka = $wtyczka->pobierzSzukaj(  ) ;

		$a = $this->view->wtyczka[0]['online'];

		$this->view->online = $a;
		$b = $this->view->wtyczka[0]['odnosnik'];
		$this->view->odnosnik = $b;

	}

	function indexAction() {
		$this->view->baseUrl = $this->_request->getBaseUrl();
		$this->view->title = "Produkty";
		$produkty = new Produkty();
		$this->view->produkty =$produkty->fetchAll();
		$kategorie = new Kategorie();
		$this->view->kategorie =$kategorie->fetchAll();
		$strony = new Strony();
		$this->view->strony = $strony->fetchAll();



	}
	function kategorieAction(){
		$oRequest = $this->getRequest();

		if( !$oRequest->getParam( 'kat_id' ) ) {

			$this->_redirect( '/produkty' );
		}


		$oProdukty = new Produkty();
		$this->view->aPrzypisaneProdukty = $oProdukty->getProduktyPrzypisaneKategoria( $oRequest->getParam( 'kat_id' ) );
		$this->view->iKatId = $oRequest->getParam( 'id' );
		$produkty = new Produkty();
		$this->view->produkty =$produkty->fetchAll();
		$kategorie = new Kategorie();
		$this->view->kategorie =$kategorie->fetchAll();
		$strony = new Strony();
		$this->view->strony = $strony->fetchAll();

	}
	function produktAction(){
		$oRequest = $this->getRequest();
		$iProdukty = $oRequest->getParam( 'pro_id' );
		$kategorie = new Kategorie();
		$this->view->kategorie =$kategorie->fetchAll();
		$iProdukty = $oRequest->getParam( 'pro_id' );
		$oProdukty = new Produkty();
		$this->view->oProdukty = $oProdukty->find( $iProdukty )->current();
		$strony = new Strony();
		$this->view->strony = $strony->fetchAll();

		$wtyczka = new Wtyczka();
		$this->view->wtyczka = $wtyczka->pobierzGalerie(  ) ;

		$a = $this->view->wtyczka[0]['online'];

		$this->view->online = $a;
		$b = $this->view->wtyczka[0]['odnosnik'];
		$this->view->odnosnik = $b;

	}



}