<?php

/**
 * Manager nazw dla plików
 * konwertuje nazwe do polskich znaków
 * sprawdza czy w podanej lokalizacji nie ma juz pliku o takiej nazwie
 * jezeli jest indeksuje nastepnym numerem   
 * 
 * @author Tomasz Skaraczyński <tom.ska@interia.pl>
 * 
 */

class FileNameManager {

  static private function _convertName($name) {
    //zamiana polskich znakow i spacji na znaki 'dopuszczalne'
    $sourceChar=array(' ','ą','ć','ę','ł','ń','ó','ś','ź','ż');
    $destinationChar=array('_','a','c','e','l','n','o','s','z','z');
    return str_replace($sourceChar, $destinationChar, mb_strtolower( $name, "UTF-8" ));
  }
  
  static private function _checkName($dir, $name) {
    //sprawdza czy podany plik nie jest juz utworzony
    //jezeli jest szuka piwerszej dobrej nazwy
    
    $fileNameParts = explode('.',$name);
    //zabezpieczenie jakby ktos w nazwie zrobil kropki...
    $countParts = count($fileNameParts);
    $fileExtension = $fileNameParts[$countParts-1];
    unset($fileNameParts[$countParts-1]); //usuwamy osatnia czesc
    $mainFileName = join('.',$fileNameParts);
    $i = 0;
    while (file_exists($dir.'/'.$name)) {
      $name = $mainFileName.'_'.$i.'.'.$fileExtension;
      $i++;
    }
    return $name;
  } 
  
  static public function getName($dir, $name) {
    //funkcja generuje poprawna nazwe dla pliku sprawdzajac przy tym, czy nie ma takiej w katalogu
    $convertName = FileNameManager::_convertName($name);
    $checkedName = FileNameManager::_checkName($dir, $convertName);
    return $checkedName;
  }
 
}

?>
