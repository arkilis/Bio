
/* A few helpful additions to manage the interaction with the new UI
 * Dominic Winsor, 22 Sept 2002
 */
 
//a few vars
var bad_characters_string = new String(); // bad characters found
var lastNterm = -1;


// this is a bit rubbish and it really breaks usability rules,
// but the user requires that n-term free is not an option for a radio set.
// should really convert to radio input, with "no modification" as default
function clearNterm( thisNterm ) {
    if (lastNterm != -1) {
        document.getElementById(lastNterm).checked = false;
    }
    lastNterm = thisNterm.id;
}

function startCalc() { // dominic's view controller
    resetCalc(); // tidy screen
    var sSeq = document.getElementById('aminoacids').value;
    var bOxidise = document.getElementById('opt_ox').checked;
    //var bNAcetyl = document.getElementById('opt_acetyl').checked;
    var bCAmide = document.getElementById('opt_amide').checked;
    //var bBiotin = document.getElementById('opt_biotin').checked;
    //var bFluor = document.getElementById('opt_fluorescein').checked;
    OutputMass(sSeq, bOxidise, bCAmide);
}

function resetCalc() {
    var output = document.getElementById('msgOut');
    var input = document.getElementById('aminoacids');
    output.value = new String();
    input.focus();  
}

function msgOut(message) { //dominic's wrapper for output
    var output = document.getElementById('msgOut');
    if (message) {
        output.value += message + "\n";
    }
    else {
        output.value += "\n";
    }
}

function copyParsedSeqToEdit( new_sequence ) { // copy string without bad chars back to edit box
    var input = document.getElementById('aminoacids');
    input.value = new_sequence.toUpperCase();
//  resetCalc();
//  startCalc();
}

// ------------------------------------------------------------




// Copyright Â© 1996 Ronald C. Beavis
// The author reserves all rights for the distribution and use
// of this script.
// Feel free to use this script in your own pages.  Please
// maintain this copyright notice on the script.
//
// Modified by Dominic Winsor of MenssanA web development 20 Sept 2002
//  - altered display code to fit site design.
//  - added facility to add phospho group to relevant amino acids.
//  - added Biotin and Fluorescein N-Term modifications
// www.get2dom.com
//
function Peptide(seq)   {
    this.sequence = "";
    this.OxidizeCysteines = OxidizeCysteines;
    this.sequence += CheckSequence(seq);
    this.length = CalculateLength(this.sequence);
    this.Mass = CalculateMass(this.sequence);
    this.ExactMass = CalculateExactMass(this.sequence);
}

function OxidizeCysteines(sequence) {
    var string_length = sequence.length;
    var current = 0;
    var cysteines = 0;
    while(current < string_length)  {
        if(sequence.charAt(current) == 'c')
            cysteines++;
        current++;
    }
    var mass_change = -2*Math.floor(cysteines/2)*10000;
    return mass_change;
}
    
function FormatFloat(float_number,n)    {
    var temp_string = "";
    temp_string += float_number;
    var limit = temp_string.length;
    var current = 0;
    var output_string = "";
    var dot_found = false;
    var char_string = "";
    var decimal_places = 0;
    while(current < limit)  {
        char_string = temp_string.charAt(current);
        if(char_string == '.')
            dot_found = true;
        if(dot_found)   {
            if(decimal_places > n)
                break;
            decimal_places++;
        }
        output_string += char_string;
        current++;
    }
    return output_string;
}


//function OutputMass(input_sequence,oxidize,n_acetyl,c_amide,n_biotin,n_fluor)   {
//
//    var sequence_string = "";
//    var current = 0;
//    CurrentPeptide = new Peptide(input_sequence);
//    if(CurrentPeptide.sequence.length == 0) {
//        msgOut("Error");
//        msgOut("The sequence contains no valid residues.");
//        msgOut("Re-enter the sequence or click in the text window.");
//        return false;
//    }
//    var mass_change = 0;
//    if(oxidize) {
//        mass_change = CurrentPeptide.OxidizeCysteines(CurrentPeptide.sequence);
//        CurrentPeptide.Mass +=  mass_change;
//        CurrentPeptide.ExactMass += mass_change;
//    }
//    if(n_acetyl)    {
//        CurrentPeptide.Mass += 420373;
//        CurrentPeptide.ExactMass += 420106;
//    }
//    if(n_biotin)    {
//        CurrentPeptide.Mass += 2260000;
//        CurrentPeptide.ExactMass += 2260000;
//    }
//    if(n_fluor) {
//        CurrentPeptide.Mass += 3580000;
//        CurrentPeptide.ExactMass += 3580000;
//    }
//    if(c_amide) {
//        CurrentPeptide.Mass += -9847;
//        CurrentPeptide.ExactMass += -9840;
//    }
//    while(current < CurrentPeptide.sequence.length) {
//        sequence_string += CurrentPeptide.sequence.substring(current,current+30);
//        sequence_string += "\n";
//        current += 30;
//    }
//    sequence_string = sequence_string.toUpperCase();
//    
//    
//    //echo sequence
//    msgOut("Sequence ("+CurrentPeptide.length+" amino acids)");
//    msgOut(sequence_string);    
//    
//    //echo mass
//    msgOut("Mass");
//    msgOut(FormatFloat(CurrentPeptide.Mass/10000,3)+" (av.) ");
//    msgOut(FormatFloat(CurrentPeptide.ExactMass/10000,3)+" (mono.)");
//    msgOut();
//    
//    //echo seq. length
////  msgOut("Length: "+CurrentPeptide.length);
////  msgOut();
//    
//    //echo characteristics
//    msgOut("Characteristics");
//    if(oxidize)
//        msgOut(" * Cys oxidized");
//    else
//        msgOut(" * Cys reduced");
//    if(n_acetyl)
//        msgOut(" * N-term acetyl");
//    else if(n_biotin)
//        msgOut(" * N-term biotin");
//    else if(n_fluor)
//        msgOut(" * N-term fluorescein");
//    else
//        msgOut(" * N-term free");
//    if(c_amide)
//        msgOut(" * C-term amide");
//    else
//        msgOut(" * C-term free");
//
//    //echo break
////  msgOut("-------------------");
//    return true;
//}

// Ben updates, only keep the 4th parameters
function OutputMass(input_sequence,oxidize, c_amide)   {

    var sequence_string = "";
    var current = 0;
    CurrentPeptide = new Peptide(input_sequence);
    if(CurrentPeptide.sequence.length == 0) {
        msgOut("Error");
        msgOut("The sequence contains no valid residues.");
        msgOut("Re-enter the sequence or click in the text window.");
        return false;
    }
    var mass_change = 0;

    if(oxidize) {
        mass_change = CurrentPeptide.OxidizeCysteines(CurrentPeptide.sequence);
        CurrentPeptide.Mass +=  mass_change;
        CurrentPeptide.ExactMass += mass_change;
    }
    if(c_amide) {
        CurrentPeptide.Mass += -9847;
        CurrentPeptide.ExactMass += -9840;
    }
    while(current < CurrentPeptide.sequence.length) {
        sequence_string += CurrentPeptide.sequence.substring(current,current+30);
        sequence_string += "\n";
        current += 30;
    }
    sequence_string = sequence_string.toUpperCase();
    
    
    //echo sequence
    msgOut("Sequence ("+CurrentPeptide.length+" amino acids)");
    msgOut(sequence_string);    
    
    //echo mass
    msgOut("Mass");
    msgOut(FormatFloat(CurrentPeptide.Mass/10000,3)+" (av.) ");
    msgOut(FormatFloat(CurrentPeptide.ExactMass/10000,3)+" (mono.)");
    msgOut();
    
    //echo seq. length
//  msgOut("Length: "+CurrentPeptide.length);
//  msgOut();
    
    //echo characteristics
    msgOut("Characteristics");
    if(c_amide)
        msgOut(" * C-term amide");
    else
        msgOut(" * C-term free");

    //echo break
//  msgOut("-------------------");
    return true;
}



function CalculateMass(input_sequence)  {
    var sequence = input_sequence;
    sequence += " ";
    var string_length = sequence.length;
    var current = 0;
    var mass = 180000;
    var aamass = 0;
    while(current < string_length)  {
        aamass = AAMass(sequence.charAt(current));
        if(aamass > 10000)
            mass += aamass;
        current += 1;
    }
    if(mass < 190000)
        return 0;
    return mass;
}
function CalculateExactMass(input_sequence) {
    var sequence = input_sequence;
    sequence += " ";
    var string_length = sequence.length;
    var current = 0;
    var mass = 180000;
    var aamass = 0;
    while(current < string_length)  {
        aamass = ExactAAMass(sequence.charAt(current));
        if(aamass > 10000)
            mass += aamass;
        current += 1;
    }
    if(mass < 190000)
        return 0;
    return mass;
}

function CalculateLength(input_sequence)    {
    var sequence = input_sequence;
    sequence += " ";
    var string_length = sequence.length;
    var current = 0;
    var aamass = 0;
    var length = 0;
    while(current < string_length)  {
        aamass = AAMass(sequence.charAt(current));
        if(aamass > 10000 && (sequence.charAt(current)!="*") ) // * does not add length, it is a modifier (see below)
            length++;
        current += 1;
    }
    return length;
}

function CheckSequence(input_sequence)  {
    var output_sequence = new String();
    var sequence = input_sequence.toLowerCase();
    var string_length = sequence.length;
    var current = 0;
    var aamass = 0;
    var bad_characters = 0;
    bad_characters_string = "";
    
    while(current < string_length)  {
        aamass = AAMass(sequence.charAt(current));
        if(aamass == 0) {
            bad_characters++;
            bad_characters_string += sequence.charAt(current)+",";
        }

        //validate the prefix to a '*' modifier (phospho)
        if( sequence.charAt(current)=="*" ) {
            var prefix_error = true;
            var prev_char = sequence.charAt(current-1);
            if( prev_char == "s" || prev_char == "t" || prev_char == "y" ) prefix_error = false;
            if (prefix_error) {
                msgOut("Phospho error");
                msgOut("'*' must follow S, T or Y amino acid");
                msgOut();
            }
        }
        
        if(aamass > 10000 ) {
            output_sequence += sequence.charAt(current);
        }
        current += 1;
    }
    if(bad_characters > 0)  {
        // this is silly, but formats a nice english sentence
        var format_s = "s";
        var format_was = "were";
        if (bad_characters==1) {
            format_s = "";
            format_was = "was";
        }
        bad_characters_string = bad_characters_string.substr(0, (bad_characters_string.length-1));
        msgOut('Note\n'+bad_characters+' non-standard abbreviation'+format_s+' ('+bad_characters_string+') '+format_was+' deleted from this sequence.');
        msgOut();
    } 
    
    copyParsedSeqToEdit( output_sequence ); // automatically update sequence without bad chars.
    return output_sequence;
}

function ExactAAMass(aa)    {
    var mass = 0;   
    if(aa == 'a')   mass =  710371;
    if(aa == 'r')   mass = 1561011;
    if(aa == 'n')   mass = 1140429;
    if(aa == 'd')   mass = 1150269;
    if(aa == 'k')   mass = 1280950;
    if(aa == 'c')   mass = 1030092;
    if(aa == 'e')   mass = 1290426;
    if(aa == 'q')   mass = 1280586;
    if(aa == 'g')   mass =  570215;
    if(aa == 'h')   mass = 1370589;
    if(aa == 'i')   mass = 1130841;
    if(aa == 'l')   mass = 1130841;
    if(aa == 'm')   mass = 1310405;
    if(aa == 'f')   mass = 1470684;
    if(aa == 'p')   mass =  970528;
    if(aa == 's')   mass =  870320;
    if(aa == 't')   mass = 1010477;
    if(aa == 'w')   mass = 1860793;
    if(aa == 'y')   mass = 1630633;
    if(aa == 'v')   mass =  990684;
    if(aa == 'b')   mass = 1145000;
    if(aa == 'z')   mass = 1285000;
    if(aa == 'x')   mass = 1110000;
    if(aa == '*')   mass =  800000; //this is for phospho group, which may only appear on a S, T or Y group.
    if(aa == ' ')   mass = 1;
    if(aa == '\t')  mass = 1;
    if(aa == '\n')  mass = 1;
    if(aa == '\r')  mass = 1;
    return mass;
}

function AAMass(aa) {
    var mass = 0;
    if(aa == 'a')   mass =  710787;
    if(aa == 'r')   mass = 1561872;
    if(aa == 'n')   mass = 1141036;
    if(aa == 'd')   mass = 1150883;
    if(aa == 'k')   mass = 1281738;
    if(aa == 'c')   mass = 1031426;
    if(aa == 'e')   mass = 1291152;
    if(aa == 'q')   mass = 1281305;
    if(aa == 'g')   mass =  570518;
    if(aa == 'h')   mass = 1371407;
    if(aa == 'i')   mass = 1131593;
    if(aa == 'l')   mass = 1131593;
    if(aa == 'm')   mass = 1311963;
    if(aa == 'f')   mass = 1471759;
    if(aa == 'p')   mass =  971164;
    if(aa == 's')   mass =  870780;
    if(aa == 't')   mass = 1011049;
    if(aa == 'w')   mass = 1862124;
    if(aa == 'y')   mass = 1631753;
    if(aa == 'v')   mass =  991323;
    if(aa == 'b')   mass = 1145000;
    if(aa == 'z')   mass = 1285000;
    if(aa == '*')   mass =  800000; //this is for phospho group, which may only appear on a S, T or Y group.
    if(aa == ' ')   mass = 1;
    if(aa == '\t')  mass = 1;
    if(aa == '\n')  mass = 1;
    if(aa == '\r')  mass = 1;
    return mass;
}
