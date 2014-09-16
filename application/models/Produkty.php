<?php
class Produkty extends Zend_Db_Table {
	protected $_name    = 'produkty';
	protected $_id = 'pro_id';
	protected $_tytul = 'pro_tytul';
	protected $_cena = 'pro_cena';
	protected $_opis = 'pro_opis';
	protected $_autor = 'pro_autor';
	protected $_logo = 'pro_logo';

	private function _losoweZdjecieSQL() {

		$sPierwszeZdjecie = sprintf(
			"select %s from %s where %s = %s order by rand() limit 1",
			'gal_plik',
			'galeria',
			'gal_pro_id',
			$this->_id
		);

		return $sPierwszeZdjecie;
	}

	public function getProduktyLosowe() {

		$sProduktZeZdjeciem = sprintf(
			"select %s id, %s tytul, %s cena, %s opis, %s autor, %s logo,  ( %s ) as plik FROM %s WHERE ( %s ) is not null ORDER BY rand() ASC LIMIT 5",
			$this->_id,
			$this->_tytul,
			$this->_cena,
			$this->_opis,
			$this->_autor,
			$this->_logo,
			$this->_losoweZdjecieSQL(),
			$this->_name,
			$this->_losoweZdjecieSQL()
		);

		$db = $this->getAdapter();
		$oZapytanie = $db->query( $sProduktZeZdjeciem );

		return $oZapytanie->fetchAll();
	}

	private function _getProduktyPrzypisaneSQL( $iKatId, $bCzyZeZdjeciem = true ) {

		$sProdukty = sprintf(
			"select %s id, %s tytul, %s cena, %s opis, %s autor, %s logo, ( %s ) as plik
			  FROM %s, %s
			  WHERE %s and %s = %s and %s = %d
			  ORDER BY tytul ASC",
			$this->_id,
			$this->_tytul,
			$this->_cena,
			$this->_opis,
			$this->_autor,
			$this->_logo,
			$this->_losoweZdjecieSQL(),
			$this->_name,
			'katpro',
			$bCzyZeZdjeciem ? '(' . $this->_losoweZdjecieSQL() . ' ) is not null': 1,
			$this->_id,
			'katpro_pro_id',
			'katpro_kat_id',
			$iKatId
		);

		return $sProdukty;
	}

	public function getProduktyKategoria( $iKatId ) {

		$sProduktZeZdjeciem = $this->_getProduktyPrzypisaneSQL( $iKatId );

		$db = $this->getAdapter();
		$oZapytanie = $db->query( $sProduktZeZdjeciem );

		return $oZapytanie->fetchAll();

	}

	public function getProduktyPrzypisaneKategoria( $iKatId ) {

		$sProdukty = $this->_getProduktyPrzypisaneSQL( $iKatId, false );

		$db = $this->getAdapter();
		$oZapytanie = $db->query( $sProdukty );

		return $oZapytanie->fetchAll();
	}

	public function getProduktyNieprzypisaneKategoria( $iKatId ) {

		$sPodzapytanie = sprintf(
			"select %s from %s where %s = %d ",
			'katpro_pro_id',
			'katpro',
			'katpro_kat_id',
			$iKatId
		);
		$sZapytanie = sprintf(
			'select %s id, %s tytul from %s where %s not in ( %s )',
			$this->_id,
			$this->_tytul,
			$this->_name,
			$this->_id,
			$sPodzapytanie
		);

		$db = $this->getAdapter();
		$oZapytanie = $db->query( $sZapytanie );

		return $oZapytanie->fetchAll();
	}



}