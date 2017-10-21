<?php

$ALLFILEs = array();	// array of all files

$letters = "ABCDEF";


//check if Manual or Auto
//==============================


$ans = $_POST['kind'];

// Automatically will create 3 files 
//==================================================

if($ans == 'auto'){

	
	
	for($i = 0; $i < 3; $i++){
		fopen("file".($i+1).".txt","x+");
	}
}

$files = scandir('/xampp\htdocs\IR\Assignment01');
$filesCount = count($files);

$inside = "";

// looping through the created files 

foreach($files as $file) {
	if(ereg(".txt$" , $file)){
  		// adding files to array ALLFILEs
		array_push($ALLFILEs,$file);							// to calc probability of each
		
		//picks 20 to 40 random letters from '$letters' String;
		for($i=0;$i<rand(20,40);$i++){
			$inside .= $letters[rand(0,strlen($letters)-1)];
		}
		
		//writing to files 
		$f = fopen($file , "w");	// open file
		
		fwrite($f,$inside);

		// closing file
		fclose($f);
	}
}

$AllProp = array();		// Multi-Dimensional Array contains array of Probabilities for each file
$Prop = array();	// Probability of each file


// Calculating probability for each file
//==================================================

for($i=0; $i<count($ALLFILEs); $i++)
{	
	//$fi = fopen(ALLFILEs[$i],'r');
	
	$filecont = file_get_contents($ALLFILEs[$i]);		// count all letters AND then get the prop
	
	$n = strlen($filecont);
	
	for($l=0;	$l< strlen($letters) ;$l++){
		$Prop[$letters[$l]] = round(substr_count($filecont,$letters[$l]) / $n *100)/100;
	}
	$AllProp[$i] = $Prop;	
}

// Query
//==========

// Checking Query:: 

// regular expression
$regex =  "^<[A-E](:(0(\.[0-9]*)?|1(\.0)?))?(;[A-E](:?(0(\.[0-9]*)?|1(\.0)?))?)*>$";
$Q = $_POST['query'];
if(ereg($regex , $Q)){
	
	// intializing the Query array
	$QProp = array_fill_keys(
				 array('A','B','C','D', 'E','F') , 
				 0);

	// removing < >
	$Q = substr($Q , 1,-1);
	// splitting Query by ;
	$QA =  explode(';' , $Q);

	// loop through All Query variables 
	for ($j=0; $j <count($QA); $j++){ 
		// break into Variable and Values
		$QAA =  explode(":",$QA[$j]);
		// Loop through the Letters ... to get the value for each character
		for ($i=0; $i < strlen($letters) ; $i++){
		// assigning values to matched letters in Query
			if($letters[$i] == $QAA[0]){
				//check if there exist a value or by default will be one (1)
				if(count($QAA) == 2)
					$QProp[$QAA[0]] = (float)$QAA[1];
				else
					$QProp[$QAA[0]] = 1;
			}
		}
	}

// Inner_product
//====================


$Scores = array();

	for ($i=0; $i < count($ALLFILEs); $i++) { 
		$Scores[$ALLFILEs[$i]] = 0;
	}

	 // loop through probabilities of each file

	 $k = 0;
	foreach($AllProp as $ff){
		
		$score = 0;
		
		for($i=0; $i < count($ff) ; $i++){ 
			$score += $ff[$letters[$i]] * $QProp[$letters[$i]];
		}
		$Scores[$ALLFILEs[$k]] = $score;
		$k++;
	}

	arsort($Scores);
	foreach ($Scores as $key => $val) {
		echo "$key = $val <br>";
	}


}else{
	echo "<b> Enter Appropiate Query! </b>";	
}

?>

