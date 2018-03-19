<?php

Class Help{
	
	public function __construct(){
		
	}
	
	function remover_caracter($string) {
		$string = preg_replace("/[áàâãä]/", "a", $string);
		$string = preg_replace("/[ÁÀÂÃÄ]/", "A", $string);
		$string = preg_replace("/[éèê]/", "e", $string);
		$string = preg_replace("/[ÉÈÊ]/", "E", $string);
		$string = preg_replace("/[íì]/", "i", $string);
		$string = preg_replace("/[ÍÌ]/", "I", $string);
		$string = preg_replace("/[óòôõö]/", "o", $string);
		$string = preg_replace("/[ÓÒÔÕÖ]/", "O", $string);
		$string = preg_replace("/[úùü]/", "u", $string);
		$string = preg_replace("/[ÚÙÜ]/", "U", $string);
		$string = preg_replace("/ç/", "c", $string);
		$string = preg_replace("/Ç/", "C", $string);
		$string = preg_replace("/[][><}{)(:;,!?*%~^`&#@]/", "", $string);
		$string = preg_replace("/ /", "_", $string);
		return $string;
	}
	
	function escreverCodigosComBR($string){
		return str_replace("\n",'<br />',htmlentities(str_replace('<br />',"\n",$string)));
	}
	
	function dtBRtoEN($data){
        $data1 = explode("/",$data);
        $data = $data1[2]."-".$data1[1]."-".$data1[0];
        return $data;
    }
    
    function dtENtoBR($data){
        $data1 = explode("-",$data);
        $data = $data1[2]."/".$data1[1]."/".$data1[0];
        return $data;
    }

    function datetimeENtoBR($data){
        $datas = explode(" ",$data);
        
        $data1 = explode("-",$datas[0]);
        $hora = explode(":",$datas[1]);
        $data = $data1[2]."/".$data1[1]."/".$data1[0]." ".$hora[0].":".$hora[1];
        return $data;
    }
    
    function arredondarParaCima($n,$x=5,$limite = 0) {
        $x = round(($n+$x/2)/$x)*$x;

        if ($x > $limite)
            return $limite;
        else
            return $x;
        
        
    }
    
    function cortar($text, $len) {
        if (strlen($text) < $len) {
            return $text;
        }
        $text_words = explode(' ', $text);
        $out = null;


        foreach ($text_words as $word) {
            if ((strlen($word) > $len) && $out == null) {

                return substr($word, 0, $len) . "...";
            }
            if ((strlen($out) + strlen($word)) > $len) {
                return $out . "...";
            }
            $out.=" " . $word;
        }
        return $out;
    }
    
    function defPosicao($pos){
		switch($pos){
				case 1:
					$x = "GOL";
					break;
				case 2:
					$x = "DEF";
					break;
				case 3:
					$x = "MEI";
					break;
				case 4:
					$x = "ATA";
					break;
		}
		return $x;
    }

    function dateafter($a){
        $hours = $a * 24;
        $added = ($hours * 3600)+time();
        //$days = date("l", $added);
        $month = date("m", $added);
        $day = date("d", $added);
        $year = date("Y", $added);
        $result = "$year-$month-$day";
        return ($result);
    }
	
	function comparaDatas($d1,$d2){
		$data_desafio 	= strtotime($d1); // converte para timestamp Unix
		$data_atual  	= strtotime($d2); // converte para timestamp Unix

		// data atual é maior que a data de expiração
		if ($data_desafio > $data_atual) // true
		  return true;
		else // false
		  return false;
	}
	
}

?>