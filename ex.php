<?php
if(isset($_POST['submit'])){
	if(isset($_POST['barcode']) and $_POST['barcode']!=''){
		$barcodes=split(",",$_POST['barcode']);
		$start=0;
		$end=count($barcodes)-1;
		$sp=',';
		$barcode=implode("','", $barcodes);
		$sql="select location,itemcallnumber,barcode from items where barcode in ('". $barcode."')";
		//echo $sql;
	}
	if(isset($_POST['barcode1']) and $_POST['barcode1']!=''){
		$barcodes=split("-",$_POST['barcode1']);
		$start=$barcodes[0];
		$end=$barcodes[1];
		$sp='-';
		$sql="select location,itemcallnumber,barcode from items where barcode >='$start' and barcode<='$end'";

	}
	require('db.php');
	require('code39.php');

	$result = $conn->query($sql);
	if(mysqli_num_rows($result)>0){
		$pdf = new PDF_Code39('L','mm',array(23,120));
		while($row = mysqli_fetch_assoc($result)){
			$shelfmark=$row['itemcallnumber'];
			$loc=$row['location'];
			$xpos=3;$ypos=5;$baseline=0.5; $height=5;		
			$code=$row['barcode'];
			$pdf->AddPage();
			$pdf->SetFont('Arial','',8);
				if(strlen($code)<=3) 
						$incr=10;
				else
					$incr=3;
				for($j=0;$j<2;$j++){
					$pdf->Text($xpos+10, $ypos-1 , 'VJCET Library');
					$pdf->Text($xpos+$incr+15, $ypos + $height + 5, $code);
					$pdf->Text($xpos+14, $ypos+$height + 9 , $shelfmark);
					$pdf->TextWithRotation($xpos, $ypos+4, $loc, 90, $font_angle=0);
					
					$pdf->Code39($xpos+$incr,$ypos,$code,1,6);
					$xpos=65;$ypos=5;$baseline=0.5; $height=5;
				}
			}	$pdf->Output();
			}
			else{ echo 'No records';}
	
}
else{
	?>
    
    <form action="" method="post">
    	<p>Barcode(CSV)
    	<input type="text" name="barcode" id="barcode" />
        </p>
    	<p>Barcode(-)
    	<input type="text" name="barcode1" id="barcode1" />
        <input type="submit" name="submit" id="submit" value="Submit"   />
    	</p>
    </form>
<?php
}
?>