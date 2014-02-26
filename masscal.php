<?php

session_start();

$output = ""; 

class Peptide
{
    function Peptide($seq)
    {
        $this->sequence="";
        //$this->OxidizeCysteines = OxidizeCysteines;
        $this->sequence .= CheckSequence($seq);
        $this->length = CalculateLength($this->sequence);
        $this->Mass = CalculateMass($this->sequence);
        $this->ExactMass = CalculateExactMass($this->sequence);
    }


    function OxidizeCysteines($sequence) {
        $string_length = strlen($sequence);
        $current    = 0;
        $cysteines  = 0;
        while($current < $string_length)  {
            if(substr($sequence, $current, 1) == 'c')
                $cysteines++;
            $current++;
        }
        $mass_change = -2*floor($cysteines/2)*10000;
        return $mass_change;
    }

}

function FormatFloat($float_number,$n)    {
	$temp_string = "";
	$temp_string .= $float_number;
	$limit = strlen($temp_string);
	$current = 0;
	$output_string = "";
	$dot_found = false;
	$char_string = "";
	$decimal_places = 0;
	
    while($current < $limit)  {
	    $char_string = substr($temp_string, $current, 1);
	    if($char_string == '.')
	    $dot_found = true;
	    if($dot_found)   {
	    if($decimal_places > $n)
	        break;
	        $decimal_places++;
	    }
    	$output_string .= $char_string;
	    $current++;
	    }
    return $output_string;
}

function OutputMass($input_sequence, $oxidize, $c_amide)   {

	$sequence_string = "";
	$current = 0;
	$CurrentPeptide = new Peptide($input_sequence);
	if(strlen($CurrentPeptide->sequence)== 0) {
	msgOut("Error");
	msgOut("The sequence contains no valid residues.");
	msgOut("Re-enter the sequence or click in the text window.");
	return false;
	}
	$mass_change = 0;
	
	if($oxidize) {
	    $mass_change = $CurrentPeptide->OxidizeCysteines($CurrentPeptide->sequence);
	    $CurrentPeptide->Mass +=  $mass_change;
	    $CurrentPeptide->ExactMass += $mass_change;
	}
	if($c_amide) {
	    $CurrentPeptide->Mass += -9847;
	    $CurrentPeptide->ExactMass += -9840;
	}
	while($current < strlen($CurrentPeptide->sequence)) {
	    $sequence_string .= substr($CurrentPeptide->sequence, $current, $current+30);
	    $sequence_string .= "<br />";
	    $current += 30;
	}
	$sequence_string = strtoupper($sequence_string);
	
	
	//echo sequence
	msgOut("Sequence (".$CurrentPeptide->length." amino acids)");
	msgOut($sequence_string);
	
	//echo mass
	msgOut("Mass");
	msgOut(FormatFloat($CurrentPeptide->Mass/10000,3)." (av.) ");
	msgOut(FormatFloat($CurrentPeptide->ExactMass/10000,3)." (mono.)");
	msgOut();
	
	//echo characteristics
	msgOut("Characteristics");
	if($c_amide)
	    msgOut(" * C-term amide");
	else
	    msgOut(" * C-term free");
	
	//echo break
	msgOut("-------------------");
	return true;
}

function CalculateMass($input_sequence)  {
    $sequence = $input_sequence;
    $sequence .= " ";
    $string_length = strlen($sequence);
    $current = 0;
    $mass = 180000;
    $aamass = 0;
    while($current < $string_length)  {
        $aamass = AAMass(substr($sequence, $current, 1));
        if($aamass > 10000)
            $mass += $aamass;
        $current += 1;
    }
    if($mass < 190000)
        return 0;
    return $mass;
}

function CalculateExactMass($input_sequence) {
    $sequence = $input_sequence;
    $sequence .= " ";
    $string_length = strlen($sequence);
    $current = 0;
    $mass = 180000;
    $aamass = 0;
    while($current < $string_length)  {
        $aamass = ExactAAMass(substr($sequence, $current, 1));
        if($aamass > 10000)
            $mass += $aamass;
        $current += 1;
    }
    if($mass < 190000)
        return 0;
    return $mass;
}

function CalculateLength($input_sequence)    {
    $sequence = $input_sequence;
    $sequence .= " ";
    $string_length = strlen($sequence);
    $current = 0;
    $aamass = 0;
    $length = 0;
    while($current < $string_length)  {
        $aamass = AAMass(substr($sequence, $current, 1));
        if($aamass > 10000 && (substr($sequence, $current, 1)!="*") ) // * does not add length, it is a modifier (see below)
            $length++;
        $current += 1;
    }
    return $length;
}

function CheckSequence($input_sequence)  {
    $output_sequence = ""; 
    $sequence = strtolower($input_sequence);
    $string_length = strlen($sequence);
    $current = 0;
    $aamass = 0;
    $bad_characters = 0;
    $bad_characters_string = "";

    while($current < $string_length)  {
        $aamass = AAMass(substr($sequence, $current, 1));
        if($aamass == 0) {
            $bad_characters++;
            $bad_characters_string .= substr($sequence, $current, 1).",";
        }

        //validate the prefix to a '*' modifier (phospho)
        if(substr($sequence, $current, 1)=="*" ) {
            $prefix_error = true;
            $prev_char = substr($sequence, $current-1, 1);
            if( $prev_char == "s" || $prev_char == "t" || $prev_char == "y" ) $prefix_error = false;
            if ($prefix_error) {
                msgOut("Phospho error");
                msgOut("'*' must follow S, T or Y amino acid");
                msgOut();
            }
        }

        if($aamass > 10000 ) {
            $output_sequence .= substr($sequence, $current, 1);
        }
        $current += 1;
    }
    if($bad_characters > 0)  {
        // this is silly, but formats a nice english sentence
        $format_s = "s";
        $format_was = "were";
        if ($bad_characters==1) {
            $format_s = "";
            $format_was = "was";
        }
        $bad_characters_string = substr($bad_characters_string, 0, (strlen($bad_characters_string)-1));
        msgOut('Note<br />'.$bad_characters.' non-standard abbreviation'.$format_s.' ('.$bad_characters_string.') '.$format_was.' deleted from this sequence.');
        msgOut();
    }

    //copyParsedSeqToEdit( $output_sequence ); // automatically update sequence without bad chars.
    return $output_sequence;
}

function ExactAAMass($aa)    {
    $mass = 0;
    if($aa == 'a')   $mass =  710371;
    if($aa == 'r')   $mass = 1561011;
    if($aa == 'n')   $mass = 1140429;
    if($aa == 'd')   $mass = 1150269;
    if($aa == 'k')   $mass = 1280950;
    if($aa == 'c')   $mass = 1030092;
    if($aa == 'e')   $mass = 1290426;
    if($aa == 'q')   $mass = 1280586;
    if($aa == 'g')   $mass =  570215;
    if($aa == 'h')   $mass = 1370589;
    if($aa == 'i')   $mass = 1130841;
    if($aa == 'l')   $mass = 1130841;
    if($aa == 'm')   $mass = 1310405;
    if($aa == 'f')   $mass = 1470684;
    if($aa == 'p')   $mass =  970528;
    if($aa == 's')   $mass =  870320;
    if($aa == 't')   $mass = 1010477;
    if($aa == 'w')   $mass = 1860793;
    if($aa == 'y')   $mass = 1630633;
    if($aa == 'v')   $mass =  990684;
    if($aa == 'b')   $mass = 1145000;
    if($aa == 'z')   $mass = 1285000;
    if($aa == 'x')   $mass = 1110000;
    if($aa == '*')   $mass =  800000; //this is for phospho group, which may only appear on a S, T or Y group.
    if($aa == ' ')   $mass = 1;
    if($aa == '\t')  $mass = 1;
    if($aa == '\n')  $mass = 1;
    if($aa == '\r')  $mass = 1;
    return $mass;
}

function AAMass($aa) {
    $mass = 0;
    if($aa == 'a')   $mass =  710787;
    if($aa == 'r')   $mass = 1561872;
    if($aa == 'n')   $mass = 1141036;
    if($aa == 'd')   $mass = 1150883;
    if($aa == 'k')   $mass = 1281738;
    if($aa == 'c')   $mass = 1031426;
    if($aa == 'e')   $mass = 1291152;
    if($aa == 'q')   $mass = 1281305;
    if($aa == 'g')   $mass =  570518;
    if($aa == 'h')   $mass = 1371407;
    if($aa == 'i')   $mass = 1131593;
    if($aa == 'l')   $mass = 1131593;
    if($aa == 'm')   $mass = 1311963;
    if($aa == 'f')   $mass = 1471759;
    if($aa == 'p')   $mass =  971164;
    if($aa == 's')   $mass =  870780;
    if($aa == 't')   $mass = 1011049;
    if($aa == 'w')   $mass = 1862124;
    if($aa == 'y')   $mass = 1631753;
    if($aa == 'v')   $mass =  991323;
    if($aa == 'b')   $mass = 1145000;
    if($aa == 'z')   $mass = 1285000;
    if($aa == '*')   $mass =  800000; //this is for phospho group, which may only appear on a S, T or Y group.
    if($aa == ' ')   $mass = 1;
    if($aa == '\t')  $mass = 1;
    if($aa == '\n')  $mass = 1;
    if($aa == '\r')  $mass = 1;
    return $mass;
}



function msgOut($message) { //dominic's wrapper for output
    if ($message!="") {
        $GLOBALS['output'].= $message."<br />";
    }
    else {
        $GLOBALS['output'].= "<br />";
    }
    //return $output;
}


///////////////////////////////////////////////////////////////////////////////////////////////////////////////

$file_path=$_POST["filename"];
$opt_ox=$_POST["opt_ox"];
$opt_amide=$_POST["opt_amide"];

if($file_path=="cal" && isset($_SESSION['filename']))
{
    foreach($_SESSION['filename'] as $filename)
    {
        $file = file_get_contents($filename, FILE_USE_INCLUDE_PATH);
        OutputMass($file, $opt_ox, $opt_amide);
        $GLOBALS['output'].="<br />==========================<br />";
    }
}



// response
// echo("<font>Results</font><br/><p>======================</p><br />");
echo($GLOBALS['output']);

// create result file
$fp= fopen("temp/".$_SESSION['username']."/temp_results.txt","w");
$content= str_replace("<br />", "\n", $GLOBALS['output']);
$content= str_replace("<BR />", "\n", $content);
fwrite($fp, $content);
fclose($fp);

echo('<br /><a href="peptide_cal/temp/'.$_SESSION['username'].'/temp_results.txt">Download Results</a><br />');
?>
