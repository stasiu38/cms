<?php

class GaleriaController extends Album_Controller_Action
{
	function init() {
		parent::init();


		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Pages');
		;
		Zend_Loader::loadClass('Galeria');
		$produkty = new Produkty();
		$this->view->produkty = $produkty->fetchAll();

	}

	function indexAction() {


		$oRequest = $this->getRequest();

		if( !$oRequest->getParam( 'pro_id' ) ) {

			$this->_redirect( 'admin/produkty' );
		}
		$this->view->oRequest = $oRequest;

		$oProdukty = new Produkty();
		$this->view->aProdukt = $oProdukty->fetchRow( "pro_id={$oRequest->getParam( 'pro_id' )}" );

		$oGaleria = new Galeria();
		$this->view->aGaleria = $oGaleria->fetchAll( "gal_pro_id={$oRequest->getParam( 'pro_id' )}" );

		$oConfig = new Zend_Config_Ini( APPLICATION_PATH . '/configs/wizytowki.ini', 'wizytowki' );
		$this->view->sWizytowkiKatalog = $oConfig->wizytowki->uri;
	}
}