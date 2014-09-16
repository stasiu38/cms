<?php
class Strony extends Zend_Db_Table {

	protected $_name    = 'strony';
	protected $_id = 'str_id';
	protected $_nazwa = 'str_nazwa';
	protected $_zawartosc = 'str_zawartosc';

	public function dopiszDoKategorii( $aZaznaczoneKategorie, $iProduktId ) {

		if( !is_array( $aZaznaczoneKategorie ) ) {

			$aZaznaczoneKategorie = array();
		}
		$aKategorie = $this->pobierzKatogorieProduktu( $iProduktId );

		foreach( $aKategorie as $aKategoria ) {

			$bCzyWTablicy = in_array( $aKategoria[ 'id' ], $aZaznaczoneKategorie );
			$bCzyZmiana = (bool)$aKategoria[ 'subid' ] ^ $bCzyWTablicy;

			if( $bCzyZmiana ) {

				if( $bCzyWTablicy ) {

					$oKatPro = new Katpro();
					$aDane = array(
						'katpro_kat_id' => $aKategoria['id'],
						'katpro_pro_id' => $iProduktId
					);
					$oKatPro->insert( $aDane );
				}
				else {

					$oKatPro = new Katpro();
					$aWhere = array(
						"katpro_pro_id={$iProduktId}",
						"katpro_kat_id={$aKategoria['id']}"
					);
					$aKatPro = $oKatPro->fetchRow( $aWhere, null );
					if( is_object( $aKatPro ) ) {

						$aKatPro->delete();
					}
				}
			}
		}

	}

	public function pobierzKatogorieProduktu( $iProduktId ) {

		$sKategorieSQL = sprintf(
			"select %s id, %s nazwa, %s subid
			from %s
			left join ( select %s from %s where %s = %d ) %s on %s = %s
			order by nazwa;",
			$this->_id,
			$this->_nazwa,
			'katpro_kat_id',
			$this->_name,
			'katpro_kat_id',
			'katpro',
			'katpro_pro_id',
			$iProduktId,
			'katpro',
			$this->_id,
			'katpro_kat_id'
		);

		$db = $this->getAdapter();
		$oZapytanie = $db->query( $sKategorieSQL );

		return $oZapytanie->fetchAll();
	}
}

