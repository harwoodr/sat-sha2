<?php

//based on http://www.ijorcs.org/manuscript/id/79/doi:10.7815/ijorcs.42.2014.079/ronglin-hao/algebraic-fault-attack-on-the-sha-256-compression-function

include("common.php");

//message = "abc"
$message = array("61626380", "00000000", "00000000", "00000000", "00000000", "00000000", "00000000", "00000000", "00000000", "00000000", "00000000", "00000000", "00000000", "00000000", "00000000", "00000018");

//hash of actual message
$hash[] = array("ba7816bf", "8f01cfea", "414140de", "5dae2223", "b00361a3", "96177a9c", "b410ff61", "f20015ad");
//hashes with fault injected at c59 
$hash[] = array("9b5290b6", "4ecc0bc8", "d4ce47b2", "e5a19f43", "d1c2fe34", "dc8cd676", "b1de26ea", "3aed6514");
$hash[] = array("d98b3e87", "8f9a7665", "24111c40", "af9d685b", "c044d37c", "70c56c37", "4a4434eb", "023eac27");
$hash[] = array("2334d195", "b84c0b8b", "7ead291d", "871e4296", "0b82850a", "31df8a76", "cccce7a9", "e40433c7");
$hash[] = array("83fcba43", "a786bfcb", "ad9ee55a", "269c2c4d", "be6a85eb", "d89d3198", "67154eef", "fd69efb9");
$hash[] = array("c45b4729", "44a2e370", "468339f9", "71f4c059", "0f4c11d1", "efab7537", "d359fc58", "915d5672");
$hash[] = array("7e955bc2", "67aa1ec1", "e5d44682", "63507566", "92567ae7", "634e7cbb", "5e3499bd", "77a59ae2");
$hash[] = array("420742e4", "757d5488", "8e4a9a0e", "dc3340b6", "f599710d", "eee0ecc8", "9e6ab026", "42dbac1e");



$start =58;

echo "(set-logic QF_BV)\n(set-info :smt-lib-version 2.0)\n";




foreach (array("a","e") as $letter){

	for($i=0;$i<4;$i++){
		echo "(declare-fun ". $letter ."_". $i ." () (_ BitVec 32))\n";
		if($letter=="e"){ $x=$i+4; } else { $x=$i;}
		echo "(assert (= ". $letter ."_". $i ." #x".$hi[$x]."))\n";
	}
}
echo "\n";

for ($i=0;$i<64;$i++){

	echo "(declare-fun w_". $i ." () (_ BitVec 32))\n";
}
echo "\n";

foreach (array("a","e") as $letter){

	for($j=0;$j<8;$j++){
		for($i=($start-3);$i<65;$i++){
			echo "(declare-fun ". $letter ."_". $i ."_". $j ." () (_ BitVec 32))\n";
		}
	}
}

for($i=0;$i<16;$i++){

	if($i!=0){
		echo "(assert (= w_".$i." #x".$message[$i]."))\n";
	}
}
/*
for($i=16;$i<64;$i++){

echo "(assert (= w_". $i ." (bvadd (bvadd w_". ($i-16) ." w_". ($i-7) .") (bvadd (bvxor (bvxor ((_ rotate_right 7) w_". ($i-15) .") ((_ rotate_right 18) w_". ($i-15) .")) (bvlshr w_". ($i-15) ." #x00000003)) (bvxor (bvxor ((_ rotate_right 17) w_". ($i-2) .") ((_ rotate_right 19) w_". ($i-2) .")) (bvlshr w_". ($i-2) ." #x0000000a)))) ))\n";
}
*/


echo "\n";
for($j=0;$j<8;$j++){

	for($i=$start;$i<64;$i++){
		echo "(assert (= a_". ($i+1) ."_".$j." (bvadd (bvadd (bvadd (bvadd ". ae_fill($i,"h",$j)." #x". $k[($i)] .") (bvadd w_". ($i) ." (bvxor (bvxor ((_ rotate_right 6) ". ae_fill($i,"e",$j).") ((_ rotate_right 11) ". ae_fill($i,"e",$j).")) ((_ rotate_right 25) ". ae_fill($i,"e",$j).")))) (bvxor (bvand ". ae_fill($i,"e",$j)." ". ae_fill($i,"f",$j).") (bvand (bvnot ". ae_fill($i,"e",$j).") ". ae_fill($i,"g",$j)."))) (bvadd (bvxor (bvxor ((_ rotate_right 2) ". ae_fill($i,"a",$j).") ((_ rotate_right 13) ". ae_fill($i,"a",$j).")) ((_ rotate_right 22) ". ae_fill($i,"a",$j).")) (bvxor (bvxor (bvand ". ae_fill($i,"a",$j)." ". ae_fill($i,"b",$j) .") (bvand ". ae_fill($i,"a",$j) ." ". ae_fill($i,"c",$j) .")) (bvand ". ae_fill($i,"b",$j) ." ". ae_fill($i,"c",$j) .")))) ))\n";
		echo "(assert (= e_". ($i+1) ."_".$j ." (bvadd ". ae_fill($i,"d",$j) ." (bvadd (bvadd (bvadd ". ae_fill($i,"h",$j) ." #x". $k[($i)] .") (bvadd w_". ($i) ." (bvxor (bvxor ((_ rotate_right 6) ". ae_fill($i,"e",$j) .") ((_ rotate_right 11) ". ae_fill($i,"e",$j) .")) ((_ rotate_right 25) ". ae_fill($i,"e",$j) .")))) (bvxor (bvand ". ae_fill($i,"e",$j) ." ". ae_fill($i,"f",$j) .") (bvand (bvnot ". ae_fill($i,"e",$j) .") ". ae_fill($i,"g",$j) .")))) ))\n\n";
	}
	for($i=61;$i<65;$i++){
		echo "(assert (= (bvadd a_".$i."_".$j." a_". (64-$i) .")  #x".$hash[$j][(64-$i)]." ))\n";

	}
	for($i=61;$i<65;$i++){
		echo "(assert (= (bvadd e_".$i."_".$j." e_". (64-$i) .") #x".$hash[$j][(68-$i)]." ))\n";
	}
	if($j>0){
		echo "(assert (= a_56_".$j." a_56_0))\n";
		echo "(assert (= e_56_".$j." e_56_0))\n";
		echo "(assert (not (= a_57_".$j." a_57_0)))\n";
		echo "(assert (= e_57_".$j." e_57_0))\n";
		echo "(assert (= a_58_".$j." a_58_0))\n";
		echo "(assert (= e_58_".$j." e_58_0))\n";
		echo "(assert (= a_59_".$j." a_59_0))\n";
		echo "(assert (= e_59_".$j." e_59_0))\n";
	}
	echo "\n";
}

echo "(check-sat)\n";


?>
