<?php
class SzukajController extends Album_Controller_Action
{
	function init() {
		parent::init();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Pages');
		Zend_Loader::loadClass('Produkty');




	}
	function indexAction(){


	}
	function wynikiAction(){

		$metoda_szukania=$_POST['metoda_szukania'];
		$wyrazenie=$_POST['wyrazenie'];

		$wyrazenie = trim($wyrazenie);

		if (!$metoda_szukania || !$wyrazenie)
		{
			echo 'Brak parametrów wyszukiwania. Wróć do poprzedniej strony i spróbuj ponownie.';
			exit;
		}

		if (!get_magic_quotes_gpc())
		{
			$metoda_szukania = addslashes($metoda_szukania);
			$wyrazenie = addslashes($wyrazenie);
		}

		@ $db = new mysqli();

		if (mysqli_connect_errno())
		{
			echo 'Błąd: Połączenie z bazą danych nie powiodło się. Spróbuj jeszcze raz później.';
			exit;
		}

		$zapytanie = "select * from produkty where ".$metoda_szukania." like '%".$wyrazenie."%'";
		$wynik = $db->query($zapytanie);

		$ile_znalezionych = $wynik->num_rows;

		echo '<p>Ilość znalezionych pozycji: '.$ile_znalezionych.'</p>';

		for ($i=0; $i <$ile_znalezionych; $i++)
		{
			$wiersz = $wynik->fetch_assoc();
			echo '<p><strong>'.($i+1).'. Tytuł: ';?>

			<a href=/produkty/produkt/pro_id/<?php echo stripslashes($wiersz['pro_id']);?>><?php echo stripslashes($wiersz['pro_tytul']);?></a><?php
			echo '</strong><br />Autor: ';
			echo stripslashes($wiersz['pro_autor']);
			echo '<br />Cena: ';
			echo stripslashes($wiersz['pro_cena']);
			echo '</p>';
		}

		$wynik->free();
		$db->close();

	}
}
