<?php
	class vector
	{
		public $x = 0.0;
		public $y = 0.0;
		public $z = 0.0;
	}
	class rotation
	{
		public $type ="";// eulers, quaterions etc
		public $unit ="";// radians, degrees
		public $x = 0.0;
		public $y = 0.0;
		public $z = 0.0;
		public $w = 0.0;
	}

	define("DEG_TO_RAD", "0.01745329238");
	define("RAD_TO_DEG", "57.29578");

	function PrintVec($vec)
	{
		echo "Vector{ ";
		echo "<span style='color:red;'>X: ". $vec->x ."</span>, ";
		echo "<span style='color:green;'>Y: ". $vec->y ."</span>, ";
		echo "<span style='color:blue;'>Z: ". $vec->z ."</span> ";
		echo "}";
	}
	
	function PrintRot($rot)
	{
		echo "Rotation{ ";
		echo "<span style='color:violet;'>type: ". $rot->type ."</span>, ";
		echo "<span style='color:darkblue;'>unit: ". $rot->unit ."</span>, ";
		echo "<span style='color:red;'>X: ". $rot->x ."</span>, ";
		echo "<span style='color:green;'>Y: ". $rot->y ."</span>, ";
		echo "<span style='color:blue;'>Z: ". $rot->z ."</span> ";
		echo "<span style='color:orange;'>W: ". $rot->w ."</span> ";
		echo "}";
	}
	
	function DegToRad($rot)
	{
		$rot->x = $rot->x*DEG_TO_RAD;
		$rot->y = $rot->y*DEG_TO_RAD;
		$rot->z = $rot->z*DEG_TO_RAD;
		$rot->w = $rot->w*DEG_TO_RAD;
		$rot->unit = "radians";
		return $rot;
	}
	
	function RadToDeg($rot)
	{
		$rot->x = $rot->x*RAD_TO_DEG;
		$rot->y = $rot->y*RAD_TO_DEG;
		$rot->z = $rot->z*RAD_TO_DEG;
		$rot->w = $rot->w*RAD_TO_DEG;
		$rot->unit = "degrees";
		return $rot;
	}
	
	function NormaliseRot($rot)
	{
		$magnitude = sqrt($rot->x*$rot->x + $rot->y*$rot->y + $rot->z*$rot->z + $rot->w*$rot->w);
		$rot->x = $rot->x/$magnitude;
		$rot->y = $rot->y/$magnitude;
		$rot->z = $rot->z/$magnitude;
		$rot->w = $rot->w/$magnitude;
		return $rot;

	}

	function EulerToRot($rot)
	{
		$rot->x = $rot->x/2;
		$rot->y = $rot->y/2;
		$rot->z = $rot->z/2;
		$rot->w = $rot->w/2;
		
		$ax = sin($rot->x);
		$aw = cos($rot->x);
		$by = sin($rot->y);
		$bw = cos($rot->y);
		$cz = sin($rot->z);
		$cw = cos($rot->z);
		
		$rot->type = "rotation";
		$rot->x = ax*bw*cw + aw*by*cz;
		$rot->y = aw*by*cw - ax*bw*cz;
		$rot->z = aw*bw*cz + ax*by*cw;
		$rot->w = aw*bw*cw - ax*by*cz;
		
		return $rot;
	}

	function RotToEuler($rot)
	{
		$newrot = new rotation();
		$newrot->x = $rot->x;
		$newrot->y = $rot->y;
		$newrot->z = $rot->z*1.0;
		
		$x = atan2(-$newrot->y,$newrot->z);
		$m = sqrt($newrot->x*$newrot->x + $newrot->y*$newrot->y + $newrot->z*$newrot->z);
		$y = asin($newrot->x/$m);
		
		$rota = new rotation(); $rota ->z = 1.0;
		$rotb = new rotation(); $rotb ->x = sin(-$x/2.0); $rotb->w = cos(-$x/2.0);
		$rotc = new rotation(); $rotc ->y = sin(-$y/2.0); $rotc->w = cos(-$y/2.0);
		
		$newrot = MultiplyRot(MultiplyRot( MultiplyRot($rota,$rot), $rotb), $rotc);
		
		$z = atan2($newrot->y, $newrot->x);
		$final = new rotation();
		$final->type = "euler";
		$final->x = $x;
		$final->y = $y;
		$final->z = $z;
		return $final;
	}

	function MultiplyRot($rota, $rotb)
	{
	$rot = new rotation();
	$rot->x = $rota->w*$rotb->x + $rota->x*$rotb->w + $rota->y*$rotb->z - $rota->z*$rotb->y;
	$rot->y = $rota->w*$rotb->y + $rota->y*$rotb->w + $rota->z*$rotb->x - $rota->x*$rotb->z;
	$rot->z = $rota->w*$rotb->z + $rota->z*$rotb->w + $rota->x*$rotb->y - $rota->y*$rotb->x;
	$rot->w = $rota->w*$rotb->w - $rota->x*$rotb->x - $rota->y*$rotb->y - $rota->z*$rotb->z;
	return $rot;
	}
	
	function MultiplyVecByRot($vec,$rot)
	{
	$rw = - $rot->x * $vec->x - $rot->y * $vec->y - $rot->z * $vec->z;
    $rx =   $rot->w * $vec->x + $rot->y * $vec->z - $rot->z * $vec->y;
    $ry =   $rot->w * $vec->y + $rot->z * $vec->x - $rot->x * $vec->z;
    $rz =   $rot->w * $vec->z + $rot->x * $vec->y - $rot->y * $vec->x;
        
    $vec->x = - $rw * $rot->x +  $rx * $rot->w - $ry * $rot->z + $rz * $rot->y;
    $vec->y = - $rw * $rot->y +  $ry * $rot->w - $rz * $rot->x + $rx * $rot->z;
    $vec->z = - $rw * $rot->z +  $rz * $rot->w - $rx * $rot->y + $ry * $rot->x;

    return $vec;
	}
	
?>

