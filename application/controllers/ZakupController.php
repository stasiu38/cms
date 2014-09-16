<?php
class ZakupController extends Album_Controller_Action {

	function init() {
		parent::init();
		$this->_helper->layout->setLayout( 'shoplayout' );
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Produkty');
		Zend_Loader::loadClass('Kategorie');
		Zend_Loader::loadClass('Zamowienia');

	}


 	function indexAction() {

		$oRequest = $this->getRequest();

		$this->view->oRequest = $oRequest;
		$strony = new Strony();
		$this->view->strony = $strony->fetchAll();
		$oRequest = $this->getRequest();
		$iProdukty = $oRequest->getParam( 'pro_id' );
		$oProdukty = new Produkty();
		$this->view->oProdukty = $oProdukty->find( $iProdukty )->current();

	}

	function  kupAction(){

		$oRequest = $this->getRequest();


		if( $oRequest->getParam( 'dos_nazwisko' ) ) {
		}
		if( $oRequest->getParam( 'dos_adres' ) ) {
		}
		if( $oRequest->getParam( 'dos_miasto' ) ) {
		}
		if( $oRequest->getParam( 'dos_wojew' ) ) {
		}
		if( $oRequest->getParam( 'dos_kod_poczt' ) ) {
		}
		if( $oRequest->getParam( 'email' ) ) {
		}

		if( (!$oRequest->getParam( 'dos_nazwisko' ))
			&& ( !$oRequest->getParam( 'dos_adres' ) )
			&& ( !$oRequest->getParam( 'dos_miasto' ) )
			&& ( !$oRequest->getParam( 'dos_wojew' ) )
			&& ( !$oRequest->getParam( 'dos_kod_poczt' ) )
			&& ( !$oRequest->getParam( 'email' ) )) {

			echo "Nie wypełniłęs pól";
		}
		else {
		$zamowienia = new Zamowienia();


			$aDane = array(
				'pro_tytul' => $oRequest ->getParam( 'pro_tytul' ),
				'wartosc' => $oRequest ->getParam( 'wartosc' ),
				'data' => $oRequest ->getParam( 'data' ),
				'dos_nazwisko' => $oRequest->getParam( 'dos_nazwisko' ),
				'dos_adres' => $oRequest->getParam( 'dos_adres' ),
				'dos_miasto' => $oRequest ->getParam( 'dos_miasto' ),
				'dos_wojew' => $oRequest ->getParam( 'dos_wojew' ),
				'dos_kod_poczt' => $oRequest ->getParam( 'dos_kod_poczt' ),
				'email' => $oRequest ->getParam( 'email' ),

			);

			$zamowienia->insert( $aDane );
			echo "Zamowienie przyjęte";
		}

	}
}
/**
 * Created by JetBrains PhpStorm.
 * User: Mariusz
 * Date: 19.08.14
 * Time: 10:24
 * To change this template use File | Settings | File Templates.
 */