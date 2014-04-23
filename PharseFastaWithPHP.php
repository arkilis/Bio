<?php
// author    Eric Aguiar
// license   GNU GPL v2
// source code available at  www.biophp.org/minitool/reader_gff_fasta

// Modified by Ben Liu
// license   GNU GPL v2
// 
// Usage: 
// include this file
// $reader= new Reader();
// $reader->setINPUT_FILE($filename);
// $reader->setTYPE_FILE("fasta");
// $reader->read();
// foreach($reader->getUID() as $id)
// {
//    $reader->Find($id)->getSequence();
// }


//[http://www.cebio.org]

//############################################################################
//#################     read file fasta and gff       ########################
//############################################################################



ini_set('memory_limit', '1000M');  //memory for reads files

class Reader{

//CONFIGURATION PARAMETERS
var $INPUT_FILE; //file input
var $TYPE_FILE; //type file
var $ERROR; 	// error in proccess
var $OBJECT; 	// content of file INPUT
var $CURRENT;  	// data of id current
var $NUMBER_LINES; //file number lines
var $UID; 		//list of ID unique
var $NUMBER_UID;//number of uniques elements
private $READ_FILE=false;
private $TYPE;


/**
	 * @return the $INPUT_FILE
	 */
	/**
	 * @return the $numberLines
	 */

	public function getNumberLines() {
		return $this->NUMBER_LINES;
	}

/**
	 * @param $numberLines the $numberLines to set
	 */
	public function setNumberLines($numberLines) {
		$this->NUMBER_LINES = $numberLines;
	}

	public function getINPUT_FILE() {
		return $this->INPUT_FILE;
	}

/**
	 * @return the $TYPE_FILE
	 */
	public function getTYPE_FILE() {
		return $this->TYPE_FILE;
	}

/**
	 * @return the $ERROR
	 */
	public function getERROR() {
		return $this->ERROR;
	}

/**
	 * @param $INPUT_FILE the $INPUT_FILE to set
	 */
	public function setINPUT_FILE($INPUT_FILE) {
		$this->INPUT_FILE = $INPUT_FILE;
		$this->CheckFileValid();
	}

/**
	 * @param $TYPE_FILE the $TYPE_FILE to set
	 */
	public function setTYPE_FILE($TYPE_FILE) {
		$this->TYPE_FILE = $TYPE_FILE;
		$this->CheckTypeValid();
	}

/**
	 * @param $ERROR the $ERROR to set
	 */
  private  function setERROR($ERROR) {
		$this->ERROR .= $ERROR;
	}
//check if file is valid
	private function CheckFileValid(){
	    if (is_file($this->getINPUT_FILE())){
	        return true;
	    }else{
	       $this->setERROR("Invalid file ($this->getINPUT_FILE())!<br>");
	       exit(1);
	    }
	}
//check if type is valid
	private function CheckTypeValid(){
	  if (strtolower($this->getTYPE_FILE())=="gff" || strtolower($this->getTYPE_FILE())=="fasta"){
	      $this->TYPE = strtolower($this->getTYPE_FILE());
	      return true;
	  }else{
	     $this->setERROR("Invalid type ($this->getTYPE_FILE())!<br>");
	     exit(1);
	  }
	}

//read file
	public function read(){
		if ($this->CheckTypeValid() && $this->CheckFileValid()){
			if ($this->TYPE=="fasta"){
				$this->readFasta();
				$this->READ_FILE=true;
			}
			if ($this->TYPE=="gff"){
				$this->readGff();
				$this->READ_FILE=true;
			}
			$this->setUID(array_unique($this->UID));
			$this->setNUMBER_UID(count($this->getUID()));
			
		}
	}

//read fasta type	
	/**
	 * @return the $UID
	 */
	public function getUID() {
		return $this->UID;
	}

	/**
	 * @return the $NUMBER_UID
	 */
	public function getNUMBER_UID() {
		return $this->NUMBER_UID;
	}

	/**
	 * @param $UID the $UID to set
	 */
	private function setUID($UID) {
		$this->UID = $UID;
	}

	/**
	 * @param $NUMBER_UID the $NUMBER_UID to set
	 */
	private function setNUMBER_UID($NUMBER_UID) {
		$this->NUMBER_UID = $NUMBER_UID;
	}

	private function readFasta (){
		$this->CheckTypeValid();
		$file = fopen($this->getINPUT_FILE(),"r");
		$contSeq = 0;
		$cont=-1;;
		
		while (!feof($file )){
			$buffer = fgets($file);
			//read header
			if ($buffer[0]==">"){
				$cont++;
				$aux="";
				$all 	= preg_split("/\s/",$buffer);
				$id 	= str_replace(">","",$all[0]);
				//$length = str_replace("length=","",$all[1]);
				//$xy 	= str_replace("xy=","",$all[2]);
				//$region = str_replace("region=","",$all[3]);
				//$run 	= str_replace("run=","",$all[4]);
				$this->OBJECT[$cont] = new fasta();
				$this->OBJECT[$cont]->setId($id);
				//$this->OBJECT[$cont]->setLength($length);
				//$this->OBJECT[$cont]->setXy($xy);
				//$this->OBJECT[$cont]->setRegion($region);
				//$this->OBJECT[$cont]->setRun($run);
				$this->UID[]=$id;
				$contSeq++;
			
			}
			else{//read sequence
				$aux.=$buffer;
				$this->OBJECT[$cont]->setSequence($aux);
				
			}
		}
		$this->setNumberLines($contSeq);
	}
//read type gff
	private function readGff(){
		$this->CheckTypeValid();
		$file = fopen($this->getINPUT_FILE(),"r");
		$contSeq = 0;
		$cont=-1;;
		
		while (!feof($file )){
			$buffer = fgets($file);
			//read header
			if ($buffer[0]!="#"){
				$cont++;
				$aux="";
				$all 		= preg_split("/\t/",$buffer);
				$seqid		= $all[0];
				if ($seqid!=""){
					$source		= $all[1];
					$type		= $all[2];
					$start		= $all[3];
					$end		= $all[4];
					$score		= $all[5];
					$strand		= $all[6];
					$phase		= $all[7];
					$attributes	= $all[8];
		
					$this->OBJECT[$cont] = new gff();
					$this->OBJECT[$cont]->setSeqid($seqid);
					$this->OBJECT[$cont]->setSource($source);
					$this->OBJECT[$cont]->setType($type);
					$this->OBJECT[$cont]->setStart($start);
					$this->OBJECT[$cont]->setEnd($end);
					$this->OBJECT[$cont]->setScore($score);
					$this->OBJECT[$cont]->setStrand($strand);
					$this->OBJECT[$cont]->setPhase($phase);
					$this->OBJECT[$cont]->setAttributes($attributes);
					$this->UID[]=$seqid;
					
					$contSeq++;
				}
			}
			
		}
		$this->setNumberLines($contSeq);
	}

// return data ($id)
	function Find($id){
		if ($this->READ_FILE){
			if ($this->TYPE=="fasta"){
				for ($i=0;$i<$this->getNumberLines();$i++){
					if (trim($this->OBJECT[$i]->getId())==trim($id)){
						$this->CURRENT = $this->OBJECT[$i];
						$found=true;
                        return  $this->OBJECT[$i];
						break;
					}
				}
			}
		
			if ($this->TYPE=="gff"){
		
				for ($i=0;$i<$this->getNumberLines();$i++){
					if (trim($this->OBJECT[$i]->getSeqid())==trim($id)){
						$this->CURRENT = $this->OBJECT[$i];
						$found=true;
                        return  $this->OBJECT[$i];
						break;
					}
				}
			}
			if (!$found) echo "<br> id ($id) not found! $this->ERROR </br>";
		}else{
			$this->setERROR("File not read");
			exit(1);
		}
	}


}


//object FASTA
class fasta {
  
    
//CLASS PARAMETERS FASTA
private $id;
private $length;
private $xy;
private $region;
private $run;
private $sequence;
/**
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}

/**
	 * @return the $lenght
	 */
	public function getLength() {
		return $this->length;
	}

/**
	 * @return the $xy
	 */
	public function getXy() {
		return $this->xy;
	}

/**
	 * @return the $region
	 */
	public function getRegion() {
		return $this->region;
	}

/**
	 * @return the $run
	 */
	public function getRun() {
		return $this->run;
	}

/**
	 * @return the $sequence
	 */
	public function getSequence() {
		return $this->sequence;
	}

/**
	 * @param $id the $id to set
	 */
	public function setId($id) {
		$this->id = $id;
	}

/**
	 * @param $lenght the $lenght to set
	 */
	public function setLength($length) {
		$this->length = $length;
	}

/**
	 * @param $xy the $xy to set
	 */
	public function setXy($xy) {
		$this->xy = $xy;
	}

/**
	 * @param $region the $region to set
	 */
	public function setRegion($region) {
		$this->region = $region;
	}

/**
	 * @param $run the $run to set
	 */
	public function setRun($run) {
		$this->run = $run;
	}

/**
	 * @param $sequence the $sequence to set
	 */
	public function setSequence($sequence) {
		$this->sequence = $sequence;
	}

	// cut sequence $ini with size $size
	public function cutSequence($ini,$size){
		$this->sequence = substr($this->sequence,$ini,$size);
		 
	}


}

//object GFF
class gff {

	//CLASS PARAMETERS GFF
private $seqid;
private $source;
private $type;
private $start;
private $end;
private $score;
private $strand;
private $phase;
private $attributes;

/**
	 * @return the $seqid
	 */
	public function getSeqid() {
		return $this->seqid;
	}

/**
	 * @return the $source
	 */
	public function getSource() {
		return $this->source;
	}

/**
	 * @return the $type
	 */
	public function getType() {
		return $this->type;
	}

/**
	 * @return the $start
	 */
	public function getStart() {
		return $this->start;
	}

/**
	 * @return the $end
	 */
	public function getEnd() {
		return $this->end;
	}

/**
	 * @return the $score
	 */
	public function getScore() {
		return $this->score;
	}

/**
	 * @return the $strand
	 */
	public function getStrand() {
		return $this->strand;
	}

/**
	 * @return the $phase
	 */
	public function getPhase() {
		return $this->phase;
	}

/**
	 * @return the $attributes
	 */
	public function getAttributes() {
		return $this->attributes;
	}

/**
	 * @param $seqid the $seqid to set
	 */
	public function setSeqid($seqid) {
		$this->seqid = $seqid;
	}

/**
	 * @param $source the $source to set
	 */
	public function setSource($source) {
		$this->source = $source;
	}

/**
	 * @param $type the $type to set
	 */
	public function setType($type) {
		$this->type = $type;
	}

/**
	 * @param $start the $start to set
	 */
	public function setStart($start) {
		$this->start = $start;
	}

/**
	 * @param $end the $end to set
	 */
	public function setEnd($end) {
		$this->end = $end;
	}

/**
	 * @param $score the $score to set
	 */
	public function setScore($score) {
		$this->score = $score;
	}

/**
	 * @param $strand the $strand to set
	 */
	public function setStrand($strand) {
		$this->strand = $strand;
	}

/**
	 * @param $phase the $phase to set
	 */
	public function setPhase($phase) {
		$this->phase = $phase;
	}

/**
	 * @param $attributes the $attributes to set
	 */
	public function setAttributes($attributes) {
		$this->attributes = $attributes;
	}

	
	
}

?>
