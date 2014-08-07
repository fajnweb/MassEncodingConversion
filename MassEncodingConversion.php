<?php 
/*
* (c)2014 Ivo Pisařovic
* MassEncodingConversion, v1.0
* Converts all text files to a new encoding.
*
* **!** It is strongly recommended to **backup** all files before running this script.
* **!** All files must be either in the entered source encoding or in the target encoding, **not mixed up** with other encodings. 
*
*/

class MassEncodingConversion{
	
	private $sourceEncoding,$targetEncoding,$exclude=array(); 
	
	public function __construct($sourceEncoding,$targetEncoding){
		$this->sourceEncoding=$sourceEncoding;
		$this->targetEncoding=$targetEncoding;
	}
	
	public function setExcluded($files){
		foreach($files as $file){
			$this->exclude[]=dirname($file);
		}
	}
	
	private function isTextFile($path){
		$mime=mime_content_type($path);
		$mime=explode("/",$mime);
		return $mime[0]=="text";
	}
	
	private function isEncoded($text){
		return mb_detect_encoding($text, $this->targetEncoding, true);
	}
	
	private function convert($path){
		$source=file_get_contents($path);
		if(!$this->isEncoded($source)){
			echo "Converted: ".$path."<br>";
			$converted=iconv($this->sourceEncoding, $this->targetEncoding, $source);
			file_put_contents($path,$converted);
			file_put_contents("win1250toutf8.log","Last converted: ".$path);
		}
	}
	
	private function processFile($dir,$file){
		$path=$dir."/".$file;
		
		if($file!="." and $file!=".." and !in_array($path,$this->exclude)){
			
			if(is_dir($path))
				$this->scan($path);
			
			elseif($this->isTextFile($path) and $path!=__DIR__."/".__FILE__) //to not convert itself
				$this->convert($path);
			
		}
	}
	
	private function scan($dir){
		$files=scandir($dir);
		foreach($files as $file){
			$this->processFile($dir,$file);
		}
	}
	
	public function run($defaultDir="."){
		$this->scan($defaultDir);
	}			   
}

//RUN
$from='windows-1250';
$to='UTF-8';

$a=new MassEncodingConversion($from,$to);
$a->setExcluded(array("soubory","./_data"));
$a->run();
