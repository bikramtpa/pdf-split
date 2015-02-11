<?php
$target_path = "./";
$target_path = $target_path . basename( $_FILES['file']['name']); 
if(move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
	split_pdf("$target_path", 'split/');
	echo "<a href='./split/'>Click to check new generated pdf files</a>";
} else{
    echo "There was an error uploading the file, please try again!";
}

function split_pdf($filename, $end_directory = false)
{
	require_once('fpdf/fpdf.php');
	require_once('fpdi/fpdi.php');
	
	$end_directory = $end_directory ? $end_directory : './';
	$new_path = preg_replace('/[\/]+/', '/', $end_directory.'/'.substr($filename, 0, strrpos($filename, '/')));
	
	if (!is_dir($new_path))
	{
		// Will make directories under end directory that don't exist
		// Provided that end directory exists and has the right permissions
		mkdir($new_path, 0777, true);
	}
	
	$pdf = new FPDI();
	$pagecount = $pdf->setSourceFile($filename); // How many pages?
	
	// Split each page into a new PDF
	for ($i = 1; $i <= $pagecount; $i++) {
		$new_pdf = new FPDI();
		$new_pdf->AddPage();
		$new_pdf->setSourceFile($filename);
		$new_pdf->useTemplate($new_pdf->importPage($i));
		
		try {
			$new_filename = $end_directory.str_replace('.pdf', '', $filename).'_'.$i.".pdf";
			$new_pdf->Output($new_filename, "F");
			echo "Page ".$i." split into ".$new_filename."<br />\n";
		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
	}
}

?>