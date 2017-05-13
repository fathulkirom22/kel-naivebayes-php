<?php  
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

/**
* library Naive Bayes
*
* @autor : Bahwell
*
* 
* Dibuat untuk prediksi mengunakan metode Naive Bayes.
* cara mengunakan:
*
* pndahkan seluruh file ke project andan
*
* incluce library, contoh: include "lib-NaiveBayes.php";
* 
*
* 1. Training data
*	 panggil fungsi NaiveBayes::train($variableIndependen,$variableDependen,$dataTraining);
*
* 2. Testing data
*	 panggil fungsi NaiveBayes::test($dataTesting);
* 
* 
*/
class NaiveBayes 
{

	static function json_pacth($url)
	{
		$data = file_get_contents($url); // put the contents of the file into a variable
		$characters = json_decode($data, TRUE); // decode the JSON feed
		return $characters;
	}

	static function hitungClass($variableIndependen,$characters)
	{
		$temp_col = array();

		foreach ($characters as $character) {
			$temp_col[] = $character[$variableIndependen];
		}

		foreach (array_unique($temp_col) as $dump_character) {
			$counts = array_count_values($temp_col);
			$Class[$dump_character] = $counts[$dump_character];
		}

		return $Class;
	}

	static function bagiClass($variableIndependen,$characters)
	{
		$classKasus = NaiveBayes::hitungClass($variableIndependen,$characters);

		foreach ($classKasus as $key => $value) {
			$temp_kasus = array(); 
			foreach ($characters as $character) {
				if ($character[$variableIndependen] == $key) {
					$temp_kasus[] = $character;
				}
			}
			$Kasus[$key] = $temp_kasus;
		}

		return $Kasus;
	}

	static function hitungKasus($variableIndependen,$variableDependen,$characters)
	{
		$Class = NaiveBayes::hitungClass($variableIndependen,$characters);
		$Kasus = NaiveBayes::bagiClass($variableIndependen,$characters);

		foreach ($Class as $key => $value) {
			$temp_hitungKasus = count($Kasus[$key]);
			foreach ($variableDependen as $key1) {
				$temp2[$key1] = NaiveBayes::hitungClass($key1,$Kasus[$key]);
			}
			$temp1[$key] = array('kasus'=>$temp2,'hitung'=>$temp_hitungKasus);
		}
		
		return $temp1;
	}

	static function training($variableIndependen,$variableDependen,$characters)
	{
		$banyak_data = count($characters);
		$temp = NaiveBayes::hitungKasus($variableIndependen,$variableDependen,$characters);
		return array('class'=>$temp,'hitung'=>$banyak_data);
	}

	static function train($variableIndependen,$variableDependen,$characters)
	{
		$trann = NaiveBayes::training($variableIndependen,$variableDependen,$characters);
		
		$myfile = fopen("train.json", "w") or die("Unable to open file!");
		fwrite($myfile, json_encode($trann));
		fclose($myfile);
		return json_encode($trann);
	}

	static function testing($test_characters)
	{
		$tarin_characters = NaiveBayes::json_pacth('train.json'); 

		foreach ($test_characters as $character) {

			$P = array();

			foreach ($tarin_characters['class'] as $keyClass => $valueClass) {
				foreach ($character as $key => $value) 
				{
					if (!empty($tarin_characters['class'][$keyClass]['kasus'][$key][$value])) 
					{
						$temp = $tarin_characters['class'][$keyClass]['kasus'][$key][$value]+1;
					}
					else
					{
						$temp = 1;
					}
					$hitungKasus = $tarin_characters['class'][$keyClass]['hitung'];
					$P[$keyClass][$key] = $temp/$hitungKasus;
				}
				$hitungClass = $tarin_characters['hitung'];
				$P[$keyClass]['class'] = $hitungKasus/$hitungClass;
			}
			$temp_Hasil[] = $P;
		}
		return $temp_Hasil;
	}

	static function testing1($test_characters)
	{
		$temp_hasil = NaiveBayes::testing($test_characters);

		foreach ($temp_hasil as $key => $value) {
			foreach ($value as $key1 => $value1) {
				$hasil_ahir_temp = 1;
				foreach ($value1 as $key2 => $value2) {
					$hasil_ahir_temp = $hasil_ahir_temp * $value2;
				}
				$hasil_ahir[$key][$key1] = $hasil_ahir_temp;
			}
		}
		return $hasil_ahir;
	}

	static function test($test_characters)
	{
		$hasil_ahir = NaiveBayes::testing1($test_characters);

		foreach ($hasil_ahir as $key => $value) {
			$temp = 0;
			$dump = "Tidak Terprediksi";
			foreach ($value as $key1 => $value1) {
				if ($temp < $value1) {
					$temp = $value1;
					$dump = $key1;
				}
			}
			$hasil_ahir_fix[] = $dump;
		}
		return $hasil_ahir_fix;
	}

}


?>