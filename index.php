<?php
include "db.php";
include "function.php";
$islem = isset($_GET["islem"]) ? addslashes(trim($_GET["islem"])) : null;
$jsonArray = array(); 


$_code = 200; 
if($_SERVER['REQUEST_METHOD'] == "POST") {
	$dateTime = new \DateTime();
 	
    $product_id = addslashes($_POST["product_id"]);
    $name = addslashes($_POST["name"]);
    $stock = addslashes($_POST["stock"]);
    $created_date = addslashes($_POST["created_date"]);


    $stoks = $db->query("SELECT * from stoks WHERE product_id='$product_id' OR created_date='$created_date'");
    
    if(empty($product_id) || empty($name) || empty($stock) || empty($created_date)) {
    	$_code = 400; 
		$jsonArray["hata"] = TRUE; 
        $jsonArray["hataMesaj"] = "Boş Alan Bırakmayınız."; 
	}
 
   else if($stoks->rowCount() !=0) {
    	$_code = 400;
        $jsonArray["hata"] = TRUE; 
        $jsonArray["hataMesaj"] = "Bu ürün daha önce yaratılmış"; 
    }else {
		$ex = $db->prepare("INSERT INTO stoks set  
			product_id= :pri, 
			name= :isim, 
			stock= :stck, 
			created_date= :zaman");
		$ekle = $ex->execute(array(
			"pri" => $product_id,
			"isim" => $name,
			"stck" => $stock,
			"zaman" => $created_date
			
		));
		if($ekle) {
			$jsonArray["code"] = "0";
			$jsonArray["msg"] = "Success";
			$jsonArray["data"] = "product_id: $product_id,name: $name,stock: $stock,created_date: $created_date";
		}else {
			$_code = 400;
			 $jsonArray["hata"] = TRUE;
       		 $jsonArray["hataMesaj"] = "Sistem Hatası.";
		}
	}
}
else if($_SERVER['REQUEST_METHOD'] == "GET") {



	
		
			
			$bilgiler = $db->query("select * from  stoks")->fetchAll(PDO::FETCH_ASSOC);
			$jsonArray["code"] = "0";
			$jsonArray["msg"] = "success";
			$jsonArray["data"] = $bilgiler;
			
	
	
}else {
	$_code = 406;
	$jsonArray["hata"] = TRUE;
 	$jsonArray["hataMesaj"] = "Geçersiz method!";
}
SetHeader($_code);
$jsonArray[$_code] = HttpStatus($_code);
echo json_encode($jsonArray);
	

?>