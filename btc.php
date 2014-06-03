<?PHP

include("common.php");

echo "(set-logic QF_BV)\n(set-info :smt-lib-version 2.0)\n";


//for http://blockexplorer.com/b/125552
$message = array("f1fc122b", "c7f5d74d", "f2b9441a", "42a14695", "80000000", "00000000", "00000000", "00000000", "00000000", "00000000", "00000000", "00000000", "00000000", "00000000", "00000000", "00000280");

//replace hi with midstate values
$hi_orig = $hi;
$hi = array("9524c593", "05c56713", "16e669ba", "2d2810a0", "07e86e37", "2f56a9da", "cd5bce69", "7a78da2d");

foreach (array("a","e") as $letter){

	for($i=0;$i<65;$i++){
	echo "(declare-fun ". $letter ."_". $i ."_1 () (_ BitVec 32))\n";

	}
}
echo "\n";
//exclude nonce word - empty the array to simply produce the hash as a code check.
$wexclude = array(3);
//$wexclude = array();
for ($i=0;$i<64;$i++){

	echo "(declare-fun w_". $i ."_1 () (_ BitVec 32))\n";
	if($i<16 && !in_array($i,$wexclude)){
		echo "(assert (= w_".$i."_1 #x".$message[$i]."))\n";
		
	} elseif($i>15){
		echo "(assert (= ".w_fill($i,1)."  (bvadd (bvadd ". w_fill($i-16,1)." ".w_fill($i-7,1).") (bvadd (bvxor (bvxor ((_ rotate_right 7) ". w_fill($i-15,1).") ((_ rotate_right 18) ". w_fill($i-15,1).")) (bvlshr ".w_fill($i-15,1)." #x00000003)) (bvxor (bvxor ((_ rotate_right 17) ".w_fill($i-2,1).") ((_ rotate_right 19) ".w_fill($i-2,1).")) (bvlshr ".w_fill($i-2,1)." #x0000000a)))) ))\n";
	}
}

echo "\n";

for($i=0;$i<64;$i++){

		echo "(assert (= a_". ($i+1) ."_1 (bvadd (bvadd (bvadd (bvadd ". ae_fill($i,"h",1)." #x". $k[($i)] .") (bvadd ". w_fill($i,1) ." (bvxor (bvxor ((_ rotate_right 6) ". ae_fill($i,"e",1).") ((_ rotate_right 11) ". ae_fill($i,"e",1).")) ((_ rotate_right 25) ". ae_fill($i,"e",1).")))) (bvxor (bvand ". ae_fill($i,"e",1)." ". ae_fill($i,"f",1).") (bvand (bvnot ". ae_fill($i,"e",1).") ". ae_fill($i,"g",1)."))) (bvadd (bvxor (bvxor ((_ rotate_right 2) ". ae_fill($i,"a",1).") ((_ rotate_right 13) ". ae_fill($i,"a",1).")) ((_ rotate_right 22) ". ae_fill($i,"a",1).")) (bvxor (bvxor (bvand ". ae_fill($i,"a",1)." ". ae_fill($i,"b",1).") (bvand ". ae_fill($i,"a",1)." ". ae_fill($i,"c",1).")) (bvand ". ae_fill($i,"b",1)." ". ae_fill($i,"c",1).")))) ))\n";
		echo "(assert (= e_". ($i+1)."_1 (bvadd ". ae_fill($i,"d",1)." (bvadd (bvadd (bvadd ". ae_fill($i,"h",1)." #x". $k[($i)] .") (bvadd ". w_fill($i,1) ." (bvxor (bvxor ((_ rotate_right 6) ". ae_fill($i,"e",1).") ((_ rotate_right 11) ". ae_fill($i,"e",1).")) ((_ rotate_right 25) ". ae_fill($i,"e",1).")))) (bvxor (bvand ". ae_fill($i,"e",1)." ". ae_fill($i,"f",1).") (bvand (bvnot ". ae_fill($i,"e",1).") ". ae_fill($i,"g",1).")))) ))\n\n";
}

$message_step1 = $message;
//first 8 words are place holders.
$message = array("00000000", "00000000", "00000000", "00000000", "00000000", "00000000", "00000000", "00000000", "80000000", "00000000", "00000000", "00000000", "00000000", "00000000", "00000000", "00000100");

$hash = array("00000000", "00000000", "00000000", "00000000", "00000000", "00000000", "00000000", "00000000");

$hi_step1 = $hi;
//get original hi array
$hi = $hi_orig;

foreach (array("a","e") as $letter){

	for($i=0;$i<65;$i++){
	echo "(declare-fun ". $letter ."_". $i ."_2 () (_ BitVec 32))\n";

	}
}

echo "\n";
$wexclude = array(0,1,2,3,4,5,6,7);
for ($i=0;$i<64;$i++){

	echo "(declare-fun w_". $i ."_2 () (_ BitVec 32))\n";
	if($i<16 && !in_array($i,$wexclude)){
		echo "(assert (= w_".$i."_2 #x".$message[$i]."))\n";
		
	} elseif($i>15){
		echo "(assert (= ".w_fill($i,2)."  (bvadd (bvadd ". w_fill($i-16,2)." ".w_fill($i-7,2).") (bvadd (bvxor (bvxor ((_ rotate_right 7) ". w_fill($i-15,2).") ((_ rotate_right 18) ". w_fill($i-15,2).")) (bvlshr ".w_fill($i-15,2)." #x00000003)) (bvxor (bvxor ((_ rotate_right 17) ".w_fill($i-2,2).") ((_ rotate_right 19) ".w_fill($i-2,2).")) (bvlshr ".w_fill($i-2,2)." #x0000000a)))) ))\n";
	} elseif(in_array($i,$wexclude)){
		//set w0-7 to output hash of first step
		if($i<4){
			echo "(assert (= w_".$i."_2 (bvadd a_".(64-$i)."_1 #x".$hi_step1[$i].") ))\n";
		} else {
			echo "(assert (= w_".$i."_2 (bvadd e_".(68-$i)."_1 #x".$hi_step1[$i].") ))\n";
		}
	}

}

echo "\n";

for($i=0;$i<64;$i++){

		echo "(assert (= a_". ($i+1) ."_2 (bvadd (bvadd (bvadd (bvadd ". ae_fill($i,"h",2)." #x". $k[($i)] .") (bvadd ". w_fill($i,2) ." (bvxor (bvxor ((_ rotate_right 6) ". ae_fill($i,"e",2).") ((_ rotate_right 11) ". ae_fill($i,"e",2).")) ((_ rotate_right 25) ". ae_fill($i,"e",2).")))) (bvxor (bvand ". ae_fill($i,"e",2)." ". ae_fill($i,"f",2).") (bvand (bvnot ". ae_fill($i,"e",2).") ". ae_fill($i,"g",2)."))) (bvadd (bvxor (bvxor ((_ rotate_right 2) ". ae_fill($i,"a",2).") ((_ rotate_right 13) ". ae_fill($i,"a",2).")) ((_ rotate_right 22) ". ae_fill($i,"a",2).")) (bvxor (bvxor (bvand ". ae_fill($i,"a",2)." ". ae_fill($i,"b",2).") (bvand ". ae_fill($i,"a",2)." ". ae_fill($i,"c",2).")) (bvand ". ae_fill($i,"b",2)." ". ae_fill($i,"c",2).")))) ))\n";
		echo "(assert (= e_". ($i+1)."_2 (bvadd ". ae_fill($i,"d",2)." (bvadd (bvadd (bvadd ". ae_fill($i,"h",2)." #x". $k[($i)] .") (bvadd ". w_fill($i,2) ." (bvxor (bvxor ((_ rotate_right 6) ". ae_fill($i,"e",2).") ((_ rotate_right 11) ". ae_fill($i,"e",2).")) ((_ rotate_right 25) ". ae_fill($i,"e",2).")))) (bvxor (bvand ". ae_fill($i,"e",2)." ". ae_fill($i,"f",2).") (bvand (bvnot ". ae_fill($i,"e",2).") ". ae_fill($i,"g",2).")))) ))\n\n";
}


for($i=0;$i<8;$i++){
	echo "(declare-fun hash_".$i." () (_ BitVec 32))\n";
	if($i<4){
		echo "(assert (= hash_".$i." (bvadd ".ae_fill((64-$i),"a",2)." #x".$hi[$i].")))\n";
	} else {
		echo "(assert (= hash_".$i." (bvadd ".ae_fill((68-$i),"e",2)." #x".$hi[$i].")))\n";
	}
}

//echo "(declare-fun x () (_ BitVec 16))\n";
//echo "(declare-fun y () (_ BitVec 16))\n";
//echo "(assert (= y #x". substr($message[3],0,4)."))\n";
//echo "(assert (= x #x0))\n";
//echo "(assert (= w_3_1 (concat y x)))\n";

echo "(assert (= hash_7 #x00000000))\n";

echo "\n";

$spread = 0x2C00;

$before = str_pad(dechex(hexdec($message_step1[3])-$spread),8,"0",STR_PAD_LEFT);
echo "(assert (bvuge w_3_1 #x".$before."))\n";
$after = str_pad(dechex(hexdec($message_step1[3])+$spread),8,"0",STR_PAD_LEFT);
echo "(assert (bvule w_3_1 #x".$after."))\n";
echo "(check-sat)\n";

