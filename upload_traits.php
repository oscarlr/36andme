<?php

function parse_database($filename){
  $mappings = array();
  $id = fopen($filename, "r"); //open the file
  $data = fgetcsv($id, filesize($filename), "\t");
  if(!$mappings){
    $mappings = $data;
  }
  while($data = fgetcsv($id, filesize($filename), "\t")){
    if($data[0]){
      foreach($data as $key => $value)
        $converted_data[$mappings[$key]] = addslashes($value);
      $arr[] = $converted_data;
    }
  }
  fclose($id); //close file
  return $arr;
  }

function parse_database_vcf_format($filename){
  $mappings = array();
  $id = fopen($filename, "r"); //open the file
  $data = fgetcsv($id, filesize($filename), "\t");
  if(!$mappings){
    $mappings = $data;
  }
  $arr = array (
		"chrom"  => array (
				   "1" => array(),
				   "2" => array(),
				   "3" => array(),
				   "4" => array(),
				   "5" => array(),
				   "6" => array(),
				   "7" => array(),
				   "8" => array(),
				   "9" => array(),
				   "10" => array(),
				   "11" => array(),
				   "12" => array(),
				   "13" => array(),
				   "14" => array(),
				   "15" => array(),
				   "16" => array(),
				   "17" => array(),
				   "18" => array(),
				   "19" => array(),
				   "20" => array(),
				   "21" => array(),
				   "22" => array()
				   )
		);
  while($data = fgetcsv($id, filesize($filename), "\t")){
    $arr["chrom"][$data[3]][$data[4]] = $data[0];
  }
  fclose($id); //close file
  return $arr;
  }

function parse_vcf($filename, $mutation_database_vcf_format){
  $mappings = array();
  $id = fopen($filename, "r"); //open the file
  $data = fgetcsv($id, filesize($filename), "\t");
  $pos = strpos($data[0],"##");
  while($pos !== false) {
    $data = fgetcsv($id, filesize($filename), "\t");
    $pos = strpos($data[0],"##");
  }
  if(!$mappings){
    $mappings = $data;
  }
  $arr = array (
		"#CHROM"  => array (
				   "1" => array(),
				   "2" => array(),
				   "3" => array(),
				   "4" => array(),
				   "5" => array(),
				   "6" => array(),
				   "7" => array(),
				   "8" => array(),
				   "9" => array(),
				   "10" => array(),
				   "11" => array(),
				   "12" => array(),
				   "13" => array(),
				   "14" => array(),
				   "15" => array(),
				   "16" => array(),
				   "17" => array(),
				   "18" => array(),
				   "19" => array(),
				   "20" => array(),
				   "21" => array(),
				   "22" => array()
				    )
		);
  while($data = fgetcsv($id, filesize($filename), "\t")){
    if($data[0]){
        if (array_key_exists($data[0], $mutation_database_vcf_format["chrom"])) {
	  if (array_key_exists($data[1], $mutation_database_vcf_format["chrom"][$data[0]])) {
	    $arr["#CHROM"][$data[0]][$data[1]] = $data[4];
	  }
	}
    }
  }
  fclose($id); //close file
  return $arr;
  }


$mutation_database = parse_database("traits_to_look_for.txt");
$mutation_database_vcf_format = parse_database_vcf_format("traits_to_look_for.txt");

$target_dir = "/sc/orga/scratch/rodrio10/papg/uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  echo "Sorry, your file was not uploaded.";
  // if everything is ok, try to upload file
} else {
  if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded." . "<br>";
  }
  else {
    echo "Sorry, there was an error uploading your file.";
  }
}

$vcf_data = parse_vcf($target_file, $mutation_database_vcf_format);

echo "<br>";
echo "<b>#####################</b><br>";
echo "---- <b>Found trait alleles</b><br>";
echo "<br>";

foreach($mutation_database as $value)
{
  $disease_chrom = $value["chrom"];
  $disease_pos = $value["pos"];
  $disease_gene = $value["gene_name"];
  $disease_name = $value["disease"];
  if (array_key_exists($disease_chrom, $vcf_data["#CHROM"])) {
    if (array_key_exists($disease_pos, $vcf_data["#CHROM"][$disease_chrom])) {
      echo $disease_name . " (gene: " . $disease_gene . ") chr" . $disease_chrom . ":" . $disease_pos . " - <b>FOUND</b><br>";
    }
  }
}

echo "<br>";
echo "----- <b>Done looking for trait alleles</b><br>";
echo "<b>#####################</b><br>";
echo "<br>";

foreach($mutation_database as $value)
{
  $disease_chrom = $value["chrom"];
  $disease_pos = $value["pos"];
  $disease_gene = $value["gene_name"];
  $disease_name = $value["disease"];
  if (array_key_exists($disease_chrom, $vcf_data["#CHROM"])) {
    if (!array_key_exists($disease_pos, $vcf_data["#CHROM"][$disease_chrom])) {
      echo $disease_name . " (gene: " . $disease_gene . ") chr" . $disease_chrom . ":" . $disease_pos . " - NOT FOUND<br>";
    }
  }
  else {
      echo $disease_name . " (gene: " . $disease_gene . ") chr" . $disease_chrom . ":" . $disease_pos . " - NOT FOUND<br>";
  }
}

?>

