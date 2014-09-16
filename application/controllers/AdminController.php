<?php
require_once 'FileNameManager.php';
require_once 'ImageTransform.php';

class AdminController extends Album_Controller_Action
{
	function init() {
		parent::init();

		$this->_helper->layout->setLayout( 'adminlayout' );
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Pages');
		Zend_Loader::loadClass('Kategorie');
		Zend_Loader::loadClass('Produkty');
		Zend_Loader::loadClass('Katpro');
		Zend_Loader::loadClass('Strony');
		Zend_Loader::loadClass('Zamowienia');
		Zend_Loader::loadClass('Galeria');
		Zend_Loader::loadClass('Wtyczka');
	}

    function indexAction() {


	echo "<p>in AdminController::indexAction()</p>";
	  $this->view->title = "Panel Administracyjny";
	   $pages = new Pages();
   $this->view->pages = $pages->fetchAll();
	if(!isset($_REQUEST['login_msg']))$_REQUEST['login_msg']='permissiondenied';



   }


	public function addAction() {

		$oRequest = $this->getRequest();

		$this->view->oRequest = $oRequest;

		$oKategorie = new Kategorie();
		$this->view->aKategorie = $oKategorie->fetchAll(null, 'kat_nazwa' );
	}

	public function addsubmitAction() {

		$oConfig = new Zend_Config_Ini( APPLICATION_PATH . '/configs/wizytowki.ini', 'wizytowki' );
		$oRequest = $this->getRequest();

		$oMimeValidator = new Zend_Validate_File_MimeType( 'image/jpg, image/jpeg' );
		$oMimeValidator->setMessage( 'Zły format pliku.' );

		$oUpload = new Zend_File_Transfer_Adapter_Http();
		$oUpload->addValidator( $oMimeValidator );
		$oUpload->getValidator('Upload')->setMessage( 'Plik jest wymagany.', Zend_Validate_File_Upload::NO_FILE);

		$bWystapilBlad = false;

		if( !$oRequest->getParam( 'nazwa' ) ) {

			$oRequest->setParam( 'error_nazwa', 'Nazwa jest wymagana.' );
			$bWystapilBlad = $bWystapilBlad || true;
		}
		if( !$oRequest->getParam( 'body' ) ) {

			$oRequest->setParam( 'error_body', 'Zawrtosc jest wymagana.' );
			$bWystapilBlad = $bWystapilBlad || true;
		}
		if( !$oRequest->getParam( 'title' ) ) {

			$oRequest->setParam( 'error_title', 'Autor jest wymagany.' );
			$bWystapilBlad = $bWystapilBlad || true;
		}

		if( !$oUpload->isValid( 'logo' ) ) {

			$aMessages = $oUpload->getMessages();
			$oRequest->setParam( 'error_logo', current($aMessages) );
			$bWystapilBlad = $bWystapilBlad || true;
		}


		if( $bWystapilBlad ) {

			return $this->_forward( 'add' );
		}

		$sPagesKatalog = $oConfig->pages->katalog;
		$sPagesNazwa = FileNameManager::getName( $sPagesKatalog, $oUpload->getFileName( 'logo', false ) );
		$oUpload->addFilter( 'Rename', $sPagesKatalog . $sPagesNazwa );
		$oUpload->receive( 'logo' );
		ImageTransform::scaleTransformImage(
			$sPagesKatalog . $sPagesNazwa,
			$oConfig->pages->logo->szerokosc,
			$oConfig->pages->logo->wysokosc,
			75, 1
		);


		$oReferencja = new Pages();
		try {

			$aDane = array(
				'name' => $oRequest->getParam( 'nazwa' ),
				'body' => $oRequest->getParam( 'body' ),
				'title' => $oRequest ->getParam( 'title' ),
				'ref_logo' => $sPagesNazwa,
			);
			$oReferencja->insert( $aDane );
		}
		catch ( Exception $e ) {

			if( $sPagesNazwa && file_exists( $sPagesKatalog . $sPagesNazwa ) ) {

				unlink( $sPagesKatalog . $sPagesNazwa );
			}


			return $this->_forward( 'admin' );
		}

		return $this->_redirect( '/' );


	}



	   function editAction() {



		   $this->view->title = "Edit pages";
		   $pages = new Pages();

		   if ($this->_request->isPost()) {
			   Zend_Loader::loadClass('Zend_Filter_StripTags');
		  $filter = new Zend_Filter_StripTags();
			$this->view->headScript()
				->appendFile( '/js/tiny_mce/tiny_mce.js',
				'text/javascript');
		  $id = (int)$this->_request->getPost('id');
		  $name = $filter->filter($this->_request->getPost('name'));
		  $name = trim($name);
			   $body = $filter->filter($this->_request->getPost('body'));
			   $body = trim($body);
		  $title = trim($filter->filter(
			  $this->_request->getPost('title')));

		  if ($id !== false) {
			  if ($name != '' && $body != '' && $title != '') {
				  $data = array(
				   'name'	=> $name,
				   'body'	=> $body,
				   'title'	=> $title,
				);
				$where = 'id = ' . $id;
				$pages->update($data, $where);

				$this->_redirect('/admin');
				return;
			 } else {
				$this->view->pages = $pages->fetchRow('id='.$id);
			 }
		  }
	   } else {
		  // pages id should be $params['$id']
		  $id = (int)$this->_request->getParam('id', 0);
		  if ($id > 0) {
			 $this->view->pages = $pages->fetchRow('id='.$id);
		  }
	   }
	   // additional view fields required by form
	   $this->view->action = 'edit';
	   $this->view->buttonText = 'Update';
	   }


   function deleteAction() {
	   $this->view->title = "Delete Pages";
	   $pages = new Pages();

	   if ($this->_request->isPost()) {
		   Zend_Loader::loadClass('Zend_Filter_Alpha');
      $filter = new Zend_Filter_Alpha();

      $id = (int)$this->_request->getPost('id');
      $del = $filter->filter($this->_request->getPost('del'));
      if ($del == 'Yes' && $id > 0) {
			   $where = 'id = ' . $id;
         $rows_affected = $pages->delete($where);
      }
   } else {
		   $id = (int)$this->_request->getParam('id');
      if ($id > 0) {
		  // only render if we have an id and can find the pages.
		  $this->view->pages = $pages->fetchRow('id='.$id);
         if ($this->view->pages->id > 0) {
			 // render template automatically
			 return;
		 }
      }
   }
	   // redirect back to the pages list unless we have rendered the view
	   $this->_redirect('/admin');

	}
	function kategorieAction(){

		$oKategorie = new Kategorie();
		$this->view->aKategorie = $oKategorie->fetchAll( null, 'kat_nazwa' );
	}
	function dodajkatAction() {

		if ($this->_request->isPost()) {
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();

			$nazwa = $filter->filter($this->_request->getPost('kat_nazwa'));
			$nazwa = trim($nazwa);


			if ($nazwa != '' ) {
				$data = array(
					'kat_nazwa' => $nazwa,
				);
				$kategorie = new Kategorie();
				$kategorie->insert($data);
				$this->_redirect('/admin/kategorie');
				return;
			}
		}
		// set up an "empty" album
		$this->view->kategorie = new stdClass();
		$this->view->kategorie->kat_id = null;
		$this->view->kategorie->kat_nazwa = '';

   		// additional view fields required by form
   		$this->view->action = 'dodajkat';
   		$this->view->buttonText = 'dodajkat';



	}
	function editkatAction() {
		$this->view->title = "Edycja Kategori";
		$kategorie = new kategorie();

		if ($this->_request->isPost()) {
			Zend_Loader::loadClass('Zend_Filter_StripTags');
      		$filter = new Zend_Filter_StripTags();

			  $kat_id = (int)$this->_request->getPost('kat_id');
			  $nazwa = $filter->filter($this->_request->getPost('kat_nazwa'));
			  $nazwa = trim($nazwa);


      if ($kat_id !== false) {
		  if ($nazwa != '') {
			  $data = array(
               'kat_nazwa'	=> $nazwa,
            );
            $where = 'kat_id = ' . $kat_id;
            $kategorie->update($data, $where);

            $this->_redirect('/admin/kategorie');
            return;
         } else {
            $this->view->kategorie=$kategorie->fetchRow('kat_id='.$kat_id);
         }
      }
   } else {
      // album id should be $params[’id’]
      $kat_id = (int)$this->_request->getParam('kat_id', 0);
      if ($kat_id > 0) {
         $this->view->kategorie = $kategorie->fetchRow('kat_id='.$kat_id);
      }
   }
   // additional view fields required by form
   $this->view->action = 'editkat';
   $this->view->buttonText = 'Update';
	}


	function deletekatAction() {
		$this->view->title = "Delete kategorie";
		$kategorie = new kategorie();

		if ($this->_request->isPost()) {
			Zend_Loader::loadClass('Zend_Filter_Alpha');
			$filter = new Zend_Filter_Alpha();

			$kat_id = (int)$this->_request->getPost('kat_id');
			$del = $filter->filter($this->_request->getPost('del'));
			if ($del == 'Yes' && $kat_id > 0) {
				$where = 'kat_id = ' . $kat_id;
				$rows_affected = $kategorie->delete($where);
			}
		} else {
			$kat_id = (int)$this->_request->getParam('kat_id');
			if ($kat_id > 0) {
				// only render if we have an id and can find the pages.
				$this->view->kategorie = $kategorie->fetchRow('kat_id='.$kat_id);
				if ($this->view->kategorie->kat_id > 0) {
					// render template automatically
					return;
				}
			}
		}
		// redirect back to the pages list unless we have rendered the view
		$this->_redirect('/admin');

	}
	function produktyAction() {


		echo "<p>in AdminController::indexAction()</p>";
		$this->view->title = "Produkty";
		$produkty = new Produkty();
		$this->view->produkty = $produkty->fetchAll();


	}

	public function addprodAction() {

		$oRequest = $this->getRequest();
		$this->view->oRequest = $oRequest;
		$kategorie = new kategorie();
		$this->view->kategorie = $kategorie->fetchAll();

	}
	public function addprodsubmitAction() {

		$oConfig = new Zend_Config_Ini( APPLICATION_PATH . '/configs/wizytowki.ini', 'wizytowki' );
		$oRequest = $this->getRequest();

		$oMimeValidator = new Zend_Validate_File_MimeType( 'image/jpg, image/jpeg' );
		$oMimeValidator->setMessage( 'Zły format pliku.' );

		$oUpload = new Zend_File_Transfer_Adapter_Http();
		$oUpload->addValidator( $oMimeValidator );
		$oUpload->getValidator('Upload')->setMessage( 'Plik jest wymagany.', Zend_Validate_File_Upload::NO_FILE);

		$bWystapilBlad = false;

		if( !$oRequest->getParam( 'pro_tytul' ) ) {

			$oRequest->setParam( 'error_tytul', 'Tytul jest wymagany.' );
			$bWystapilBlad = $bWystapilBlad || true;
		}
		if( !$oRequest->getParam( 'pro_autor' ) ) {

			$oRequest->setParam( 'error_autor', 'Autor jest wymagany.' );
			$bWystapilBlad = $bWystapilBlad || true;
		}
		if( !$oRequest->getParam( 'pro_opis' ) ) {

			$oRequest->setParam( 'error_opis', 'Którki opis jest wymagany.' );
			$bWystapilBlad = $bWystapilBlad || true;
		}
		if( !$oRequest->getParam( 'pro_tekst' ) ) {

			$oRequest->setParam( 'error_tekst', 'Text jest wymagany.' );
			$bWystapilBlad = $bWystapilBlad || true;
		}
		if( !$oUpload->isValid( 'logo' ) ) {

			$aMessages = $oUpload->getMessages();
			$oRequest->setParam( 'error_logo', current($aMessages) );
			$bWystapilBlad = $bWystapilBlad || true;
		}

		if( !$oRequest->getParam( 'pro_cena' ) ) {

			$oRequest->setParam( 'error_cena', 'Cena jest wymagana.' );
			$bWystapilBlad = $bWystapilBlad || true;
		}



		/*if( !$oUpload->isValid( 'zdjecie' ) ) {

			$aMessages = $oUpload->getMessages();
			$oRequest->setParam( 'error_zdjecie', current($aMessages) );
			$bWystapilBlad = $bWystapilBlad || true;
		}*/


		if( $bWystapilBlad ) {

			return $this->_forward( 'addprod' );
		}

		$sLogoKatalog = $oConfig->produkty->logo->katalog;
		$sLogoNazwa = FileNameManager::getName( $sLogoKatalog, $oUpload->getFileName( 'logo', false ) );
		$oUpload->addFilter( 'Rename', $sLogoKatalog . $sLogoNazwa );
		$oUpload->receive( 'logo' );
		ImageTransform::scaleTransformImage(
			$sLogoKatalog . $sLogoNazwa,
			$oConfig->produkty->logo->szerokosc,
			$oConfig->produkty->logo->wysokosc,
			75, 1
		);


		$oProdukty = new Produkty();
		try {

			$aDane = array(
				'pro_tytul' => $oRequest->getParam( 'pro_tytul' ),
				'pro_autor' => $oRequest->getParam( 'pro_autor' ),
				'pro_opis' => $oRequest ->getParam( 'pro_opis' ),
				'pro_tekst' => $oRequest ->getParam( 'pro_tekst' ),
				'pro_cena' => $oRequest ->getParam( 'pro_cena' ),
				'pro_logo' => $sLogoNazwa,
			);
			$iProId = $oProdukty->insert( $aDane );

			$okategorie = new kategorie();
			$okategorie->dopiszDoKategorii( $oRequest->getParam( 'kategorie' ), $iProId );

			$this->_redirect( 'admin/produkty' );
		}
		catch ( Exception $e ) {

			if( $sLogoNazwa && file_exists( $sLogoKatalog . $sLogoNazwa ) ) {

				unlink( $sLogoKatalog . $sLogoNazwa );
			}

			echo 'Wystąpił błąd w linii '.$e->getLine().': '.$e->getMessage();


		}




	}
	public function prodeditAction(){

		$this->view->title = "Edycja produktu";
		$produkty = new Produkty();

		if ($this->_request->isPost()) {
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();

			$pro_id = (int)$this->_request->getPost('pro_id');
			$tytul = $filter->filter($this->_request->getPost('pro_tytul'));
			$tytul = trim($tytul);
			$autor = $filter->filter($this->_request->getPost('autor'));
			$autor = trim($autor);
			$opis = $filter->filter($this->_request->getPost('pro_opis'));
			$opis = trim($opis);
			$tekst = $filter->filter($this->_request->getPost('pro_tekst'));
			$tekst = trim($tekst);
			$cena = $filter->filter($this->_request->getPost('pro_cena'));
			$cena = trim($cena);



			if ($pro_id !== false) {
				if ($tytul != '' && $autor != '' && $opis != '' && $tekst != '' && $cena != '') {
					$data = array(
						'pro_tytul'	=> $tytul,
						'pro_autor'	=> $autor,
						'pro_opis'	=> $opis,
						'pro_tekst'	=> $tekst,
						'pro_cena' => $cena
					);
					$where = 'pro_id = ' . $pro_id;
					$produkty->update($data, $where);

					$this->_redirect('/admin/strony');
					return;
				} else {
					$this->view->strony=$produkty->fetchRow('pro_id='.$pro_id);
				}
			}
		} else {
			// album id should be $params[’id’]
			$pro_id = (int)$this->_request->getParam('pro_id', 0);
			if ($pro_id > 0) {
				$this->view->produkty = $produkty->fetchRow('pro_id='.$pro_id);
			}
		}
		// additional view fields required by form
		$this->view->action = 'proedit';
		$this->view->buttonText = 'Aktualizuj';
	}

	public function proddeleteAction(){

		$this->view->title = "Usuń produkt";
		$produkty = new Produkty();

		if ($this->_request->isPost()) {
			Zend_Loader::loadClass('Zend_Filter_Alpha');
			$filter = new Zend_Filter_Alpha();

			$pro_id = (int)$this->_request->getPost('pro_id');
			$del = $filter->filter($this->_request->getPost('del'));
			if ($del == 'Yes' && $pro_id > 0) {
				$where = 'pro_id = ' . $pro_id;
				$rows_affected = $produkty->delete($where);
			}
		} else {
			$pro_id = (int)$this->_request->getParam('pro_id');
			if ($pro_id > 0) {
				// only render if we have an id and can find the pages.
				$this->view->produkty = $produkty->fetchRow('pro_id='.$pro_id);
				if ($this->view->produkty->pro_id > 0) {
					// render template automatically
					return;
				}
			}
		}
		// redirect back to the pages list unless we have rendered the view
		$this->_redirect('/admin');

	}



	public function produktykategorieAction() {

		$oRequest = $this->getRequest();

		if( !$oRequest->getParam( 'pro_id' ) ) {

			$this->_redirect( 'admin/produkty' );
		}

		$this->view->oRequest = $oRequest;

		$oKategorie = new Kategorie();
		$this->view->aKategorie = $oKategorie->pobierzKatogorieProduktu( $oRequest->getParam( 'id' ) );
	}

	public function produktykategoriesubmitAction() {

		$oRequest = $this->getRequest();

		$oKategorie = new Kategorie();
		$oKategorie->dopiszDoKategorii( $oRequest->getParam( 'kategorie' ), $oRequest->getParam( 'id' ) );

		$this->_redirect( 'administrator/produkty' );
	}
	public function kategorieproduktyAction() {

		$oRequest = $this->getRequest();

		if( !$oRequest->getParam( 'id' ) ) {

			$this->_redirect( 'administrator/kategorie' );
		}

		$oProdukty = new Produkty();
		$this->view->aPrzypisaneProdukty = $oProdukty->getProduktyPrzypisaneKategoria( $oRequest->getParam( 'id' ) );
		$this->view->aNieprzypisaneProdukty = $oProdukty->getProduktyNieprzypisaneKategoria( $oRequest->getParam( 'id' ) );

		$this->view->iKatId = $oRequest->getParam( 'id' );
	}
	public function kategoriedopiszproduktAction() {

		$oRequest = $this->getRequest();

		if( !$oRequest->getParam( 'kat_id') || !$oRequest->getParam( 'pro_id') ) {

			$this->redirect( 'admin/kategorieprodukty?id='.$oRequest->getParam( 'kat_id') );
		}

		$oKatPro = new Katpro();
		$aDane = array(
			'katpro_kat_id' => $oRequest->getParam( 'kat_id'),
			'katpro_pro_id' => $oRequest->getParam( 'pro_id')
		);
		$oKatPro->insert( $aDane );

		$this->_redirect( 'admin/kategorieprodukty?id='.$oRequest->getParam( 'kat_id') );

	}
	public function kategorieusunproduktAction() {

		$oRequest = $this->getRequest();

		if( !$oRequest->getParam( 'kat_id') || !$oRequest->getParam( 'pro_id') ) {

			$this->redirect( 'admin/kategorieprodukty?id='.$oRequest->getParam( 'kat_id') );
		}

		$oKatPro = new Katpro();
		$aSzukane = array(
			"katpro_kat_id = {$oRequest->getParam( 'kat_id')}",
			"katpro_pro_id = {$oRequest->getParam( 'pro_id')}"
		);
		$aKatPro = $oKatPro->fetchRow( $aSzukane, null );
		$aKatPro->delete();

		$this->_redirect( 'admin/kategorieprodukty?id='.$oRequest->getParam( 'kat_id') );
	}

	public function stronyAction()
	{
		$this->view->title = "Panel Administracyjny";
		$strony = new Strony();
		$this->view->strony = $strony->fetchAll();
	}
	public function stronyaddAction()
	{
		if ($this->_request->isPost()) {
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();

			$nazwa = $filter->filter($this->_request->getPost('str_nazwa'));
			$nazwa = trim($nazwa);
			$zawartosc = $filter->filter($this->_request->getPost('str_zawartosc'));
			$zawartosc = trim($zawartosc);


			if ($nazwa != '' && $zawartosc != '') {
				$data = array(
					'str_nazwa' => $nazwa,
					'str_zawartosc' => $zawartosc,
				);
				$strony = new Strony();
				$strony->insert($data);
				$this->_redirect('/admin/strony');
				return;
			}
		}
		// set up an "empty" album
		$this->view->strony = new stdClass();
		$this->view->strony->str_id = null;
		$this->view->strony->str_nazwa = '';
		$this->view->strony->str_zawartosc = '';

		// additional view fields required by form
		$this->view->action = 'stronyadd';
		$this->view->buttonText = 'Dodaj';
	}

	public function stronyeditAction()
	{
		$this->view->title = "Edycja Strony";
		$strony = new Strony();

		if ($this->_request->isPost()) {
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();

			$str_id = (int)$this->_request->getPost('str_id');
			$nazwa = $filter->filter($this->_request->getPost('str_nazwa'));
			$nazwa = trim($nazwa);
			$zawartosc = $filter->filter($this->_request->getPost('str_zawartosc'));
			$zawartosc = trim($zawartosc);


			if ($str_id !== false) {
				if ($nazwa != '' && $zawartosc != '') {
					$data = array(
						'str_nazwa'	=> $nazwa,
						'str_zawartosc'	=> $zawartosc
					);
					$where = 'str_id = ' . $str_id;
					$strony->update($data, $where);

					$this->_redirect('/admin/strony');
					return;
				} else {
					$this->view->strony=$strony->fetchRow('str_id='.$str_id);
				}
			}
		} else {
			// album id should be $params[’id’]
			$str_id = (int)$this->_request->getParam('str_id', 0);
			if ($str_id > 0) {
				$this->view->strony = $strony->fetchRow('str_id='.$str_id);
			}
		}
		// additional view fields required by form
		$this->view->action = 'stronyedit';
		$this->view->buttonText = 'Aktualizuj';
	}
	function stronydeleteAction() {
		$this->view->title = "Usuń strone";
		$strony = new Strony();

		if ($this->_request->isPost()) {
			Zend_Loader::loadClass('Zend_Filter_Alpha');
			$filter = new Zend_Filter_Alpha();

			$str_id = (int)$this->_request->getPost('str_id');
			$del = $filter->filter($this->_request->getPost('del'));
			if ($del == 'Yes' && $str_id > 0) {
				$where = 'str_id = ' . $str_id;
				$rows_affected = $strony->delete($where);
			}
		} else {
			$str_id = (int)$this->_request->getParam('str_id');
			if ($str_id > 0) {
				// only render if we have an id and can find the pages.
				$this->view->strony = $strony->fetchRow('str_id='.$str_id);
				if ($this->view->strony->str_id > 0) {
					// render template automatically
					return;
				}
			}
		}
		// redirect back to the pages list unless we have rendered the view
		$this->_redirect('/admin');

	}
	function zamowieniaAction(){
		$this->view->title = "Zamowienia";
		$zamowienia = new Zamowienia();
		$this->view->zamowienia = $zamowienia->fetchAll();

	}
	function zamowieniadeleteAction(){
		$this->view->title = "Delete Zamowienia";
		$zamowienia = new Zamowienia();

		if ($this->_request->isPost()) {
			Zend_Loader::loadClass('Zend_Filter_Alpha');
			$filter = new Zend_Filter_Alpha();

			$idzamowienia = (int)$this->_request->getPost('idzamowienia');
			$del = $filter->filter($this->_request->getPost('del'));
			if ($del == 'Yes' && $idzamowienia > 0) {
				$where = 'idzamowienia = ' . $idzamowienia;
				$rows_affected = $zamowienia->delete($where);
			}
		} else {
			$idzamowienia = (int)$this->_request->getParam('idzamowienia');
			if ($idzamowienia > 0) {
				// only render if we have an id and can find the pages.
				$this->view->zamowienia = $zamowienia->fetchRow('idzamowienia='.$idzamowienia);
				if ($this->view->zamowienia->idzamowienia > 0) {
					// render template automatically
					return;
				}
			}
		}
		// redirect back to the pages list unless we have rendered the view
		$this->_redirect('/admin/zamowienia/');

	}

	function galeriaAction(){

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
		$strony = new Strony();
		$this->view->strony = $strony->fetchAll();

	}
	public function dodajgaleriaAction($filefield) {

		$upload = new Zend_File_Transfer_Adapter_Http();
		$fileinfo = $upload->getFileInfo();
		$oRequest = $this->getRequest();

		if( !$oRequest->getParam( 'pro_id' ) ) {

			$this->_redirect( 'adminn/produkty' );
		}

		//walidacja pola zdjecie
		$oMimeValidator = new Zend_Validate_File_MimeType( 'image/jpg, image/jpeg' );
		$oMimeValidator->setMessage( 'Zły format pliku.' );

		$oUpload = new Zend_File_Transfer_Adapter_Http();
		$oUpload->addValidator( $oMimeValidator );
		$oUpload->getValidator('Upload')->setMessage( 'Plik jest wymagany.', Zend_Validate_File_Upload::NO_FILE);



		try {

			$oConfig = new Zend_Config_Ini( APPLICATION_PATH . '/configs/wizytowki.ini', 'wizytowki' );

			$sWizytowkiKatalog = $oConfig->wizytowki->katalog;
			$sWizytowkaNazwa = FileNameManager::getName( $sWizytowkiKatalog, $oUpload->getFileName( 'zdjecie', false ) );
			$oUpload->addFilter( 'Rename', $sWizytowkiKatalog . $sWizytowkaNazwa );
			$oUpload->receive($fileinfo[$filefield]['zdjecie']);




			$oGaleria = new Galeria();
			$aDane = array(
				'gal_plik' => $sWizytowkaNazwa,
				'gal_pro_id' => $oRequest->getParam( 'pro_id' )
			);
			$oGaleria->insert( $aDane );

		}
		catch ( Exception $oException ) {

			if( $sWizytowkaNazwa && file_exists( $sWizytowkiKatalog . $sWizytowkaNazwa ) ) {

				unlink( $sWizytowkiKatalog . $sWizytowkaNazwa );
			}

			$oRequest->setParam( 'error_zdjecie', 'Bład podczas zapisu.' );
			return $this->_forward( 'dodajzdjecia' );
		}

		$this->_redirect( 'admin/galeria?pro_id='.$oRequest->getParam( 'pro_id' ) );
	}
	public function zdjecieusunAction() {

		$oRequest = $this->getRequest();

		if( !$oRequest->getParam( 'gal_id' ) || !$oRequest->getParam( 'pro_id' ) ) {

			$this->_redirect( 'admin/produkty' );
		}

		$oGaleria = new Galeria();
		$oZdjecie = $oGaleria->fetchRow( "gal_id={$oRequest->getParam( 'gal_id' )}" );

		if( is_object( $oZdjecie ) ) {

			$oConfig = new Zend_Config_Ini( APPLICATION_PATH . '/configs/wizytowki.ini', 'wizytowki' );

			$sWizytowkiKatalog = $oConfig->wizytowki->katalog;
			unlink( $sWizytowkiKatalog . $oZdjecie->gal_plik );
			$oZdjecie->delete();
		}

		$this->_redirect( 'admin/galeria?pro_id='.$oRequest->getParam( 'pro_id' ) );
	}

	function wtyczkaAction(){
		$this->view->title = "Wtyczki";
		$wtyczka = new Wtyczka();
		$this->view->wtyczka = $wtyczka->fetchAll();
	}

	function addwtyczkaAction(){

		$oRequest = $this->getRequest();

		if( !$oRequest->getParam( 'wt_id' ) ) {

			$this->_redirect( 'admin/produkty' );
		}

		$this->view->oRequest = $oRequest;

		$oWtyczka = new Wtyczka();
		$this->view->aWtyczka = $oWtyczka->fetchRow( "wt_id={$oRequest->getParam( 'wt_id' )}" );

		$oConfig = new Zend_Config_Ini( APPLICATION_PATH . '/configs/wizytowki.ini', 'wizytowki' );
		$this->view->sWizytowkiKatalog = $oConfig->wizytowki->uri;


	}
	function addsubwtyczkaAction()
	{
		$oRequest = $this->getRequest();
		if( !$oRequest->getParam( 'wt_id' ) ) {

			$this->_redirect( 'admin/' );
		}

				$zip = new ZipArchive();
				if (is_uploaded_file($_FILES['file']['tmp_name'])) {
				?>
			<table border="1" cellspacing="5" cellpadding="5">
				<tr>
					<td><b>Filename</b></td>
					<td><b>Uncompressed size</b></td>
					<td><b>Compressed size</b></td>
					<td><b>Pack ratio</b></td>
					<td><b>Last Modified</b></td>

					<?PHP
					$filename = $_FILES['file']['tmp_name'];
					$wt_id = (int)$this->_request->getPost('wt_id');
					$online = 1;
					// open uploaded file
					if ($zip->open($filename, ZIPARCHIVE::CREATE) !== TRUE) {
						die ("Could not open archive");
					}
					$zip->extractTo('D:\mojestrony\cms');
					// get number of files



					$numFiles = $zip->numFiles;

					// iterate over file list
					// print details of each file
					for ($x=0; $x<$numFiles; $x++) {
						$file = $zip->statIndex($x);
						echo "<tr>";
						echo "<td>" . $file['name'] . "</td>";
						echo "<td>" . $file['size'] . "</td>";
						echo "<td>" . $file['comp_size'] . "</td>";
						if ($file['size'] > 0) {
							echo "<td>" . sprintf("%3.2f", (($file['size'] - $file['comp_size']) / $file['size']) * 100)  . " %</td>";
						} else {
							echo "<td>-</td>";
						}
						echo "<td>" . date("d M Y h:i", $file['mtime']) . "</td>";
						echo "</tr>";
					}
					$wtyczka = new Wtyczka();
					if ($wt_id !== false) {
						if ($filename != '') {
								$data = array(
								'odnosnik'	=>  $file['name'],
								'online'	=> $online
							);
							$where = 'wt_id = ' . $wt_id;
							$wtyczka->update($data, $where);

							$this->_redirect('/admin/wtyczka');
							return;
						}}

					// close archive
					$zip->close();
					?>
			</table>
			<?PHP
			} else {
				die ('Invalid file!');
			}
	}
	function wlaczAction(){

		$oRequest = $this->getRequest();

		if( !$oRequest->getParam( 'wt_id' ) ) {

			$this->_redirect( 'admin/wtyczka' );
		}

		$this->view->oRequest = $oRequest;

		$oWtyczka = new Wtyczka();
		$this->view->aWtyczka = $oWtyczka->fetchRow( "wt_id={$oRequest->getParam( 'wt_id' )}" );

		$oConfig = new Zend_Config_Ini( APPLICATION_PATH . '/configs/wizytowki.ini', 'wizytowki' );
		$this->view->sWizytowkiKatalog = $oConfig->wizytowki->uri;


	}
	function wlaczwtyczkaAction(){

		$oRequest = $this->getRequest();
		if( !$oRequest->getParam( 'wt_id' ) ) {

			echo 'problem z pobraniem parametru';
		}
		$wt_id = (int)$this->_request->getPost('wt_id');
		$online = 2;
		$wtyczka = new Wtyczka();
		if ($wt_id !== false) {
			if ($online != '') {
				$data = array(

					'online'	=> $online
				);
				$where = 'wt_id = ' . $wt_id;
				$wtyczka->update($data, $where);
				$this->_redirect('/admin/wtyczka');

				;
				return;
			}
		}

		}

	function wylaczAction(){

		$oRequest = $this->getRequest();

		if( !$oRequest->getParam( 'wt_id' ) ) {

			$this->_redirect( 'admin/wtyczka' );
		}

		$this->view->oRequest = $oRequest;

		$oWtyczka = new Wtyczka();
		$this->view->aWtyczka = $oWtyczka->fetchRow( "wt_id={$oRequest->getParam( 'wt_id' )}" );

		$oConfig = new Zend_Config_Ini( APPLICATION_PATH . '/configs/wizytowki.ini', 'wizytowki' );
		$this->view->sWizytowkiKatalog = $oConfig->wizytowki->uri;


	}


		function wylaczwtyczkaAction(){

			$oRequest = $this->getRequest();
			if( !$oRequest->getParam( 'wt_id' ) ) {

				echo 'problem z pobrnie parametru';
			}
			$wt_id = (int)$this->_request->getPost('wt_id');
			$onlinee = 1;
			$wtyczka = new Wtyczka();
			if ($wt_id !== false) {
				if ($onlinee != '') {
					$data = array(

						'online'	=> $onlinee
					);
					$where = 'wt_id = ' . $wt_id;
					$wtyczka->update($data, $where);
					$this->_redirect('/admin/wtyczka');

					;
					return;


				}
			}


}}