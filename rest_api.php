<?php
require_once "config.php";
$request_method=$_SERVER["REQUEST_METHOD"];

switch ($request_method) {
   case 'GET':
         if(!empty($_GET["nim"])) {
			 if(!empty($_GET["kode_mk"])) {
				 get_nilai_mhs_view($_GET["nim"], $_GET["kode_mk"]);
			 } else {
				 $nim=$_GET["nim"];
				 get_nilai_mhs($nim);
			 }
            
         }
         else {
            get_mhsAll();
         }
         break;
         
   case 'POST':
         if(!empty($_GET["nim"])) {
            $nim=$_GET["nim"];
            $kode_mk=$_GET["kode_mk"];
            update_nilai_mhs($nim, $kode_mk);
         }
         else {
            insert_nilai_mhs();
         }     
         break; 
         
   case 'DELETE':
         if(!empty($_GET["nim"]) && !empty($_GET["kode_mk"])) {
			  $nim=$_GET["nim"];
			  $kode_mk=$_GET["kode_mk"];
			  delete_nilai_mhs($nim, $kode_mk);
		 } else {
			 $response=array(
			   'status' => 0,
			   'message' =>'Nilai Mahasiswa Deletion Failed.'
			 );
			 header('Content-Type: application/json');
			 echo json_encode($response);
		 }
          break;
   default:
      // Invalid Request Method
         header("HTTP/1.0 405 Method Not Allowed");
         break;
      break;
 }



   function get_mhsAll() {
      global $mysqli;
      $query = "SELECT mahasiswa.*, matakuliah.kode_mk, matakuliah.nama_mk, matakuliah.sks, perkuliahan.nilai FROM mahasiswa INNER JOIN perkuliahan ON perkuliahan.nim = mahasiswa.nim INNER JOIN matakuliah ON matakuliah.kode_mk = perkuliahan.kode_mk;";
      $data=array();
      $result=$mysqli->query($query);
      while($row=mysqli_fetch_object($result)) {
         $data[]=$row;
      }
      $response=array(
                     'status' => 1,
                     'message' =>'Get List Mahasiswa Successfully.',
                     'data' => $data
                  );
      header('Content-Type: application/json');
      echo json_encode($response);
   }
   
   function get_nilai_mhs_view($nim, $kode_mk) {
      global $mysqli;
      $query="SELECT
    mahasiswa.nim,
    mahasiswa.nama,
	matakuliah.kode_mk,
    perkuliahan.nilai
FROM mahasiswa INNER JOIN perkuliahan ON perkuliahan.nim = mahasiswa.nim INNER JOIN matakuliah ON matakuliah.kode_mk = perkuliahan.kode_mk WHERE mahasiswa.nim = '".$nim."' AND matakuliah.kode_mk='".$kode_mk."'";
      $data=array();
      $result=$mysqli->query($query);
      while($row=mysqli_fetch_object($result))
      {
         $data[]=$row;
      }
      $response=array(
                     'status' => 1,
                     'message' =>'Get Nilai Mahasiswa Successfully.',
                     'data' => $data
                  );
      header('Content-Type: application/json');
      echo json_encode($response);
        
   }
 
   function get_nilai_mhs($nim) {
      global $mysqli;
      $query="SELECT
    mahasiswa.nim,
    mahasiswa.nama,
    perkuliahan.nilai
FROM
    perkuliahan
INNER JOIN mahasiswa ON perkuliahan.nim = perkuliahan.nim WHERE mahasiswa.nim = '".$nim."'";
      $data=array();
      $result=$mysqli->query($query);
      while($row=mysqli_fetch_object($result))
      {
         $data[]=$row;
      }
      $response=array(
                     'status' => 1,
                     'message' =>'Get Nilai Mahasiswa Successfully.',
                     'data' => $data
                  );
      header('Content-Type: application/json');
      echo json_encode($response);
        
   }
 
   function insert_nilai_mhs() {
         global $mysqli;
         if(!empty($_POST["nim"])){
            $data=$_POST;
         } else {
            $data = json_decode(file_get_contents('php://input'), true);
         }

         $arrcheckpost = array('nim' => '','kode_mk' => '','nilai' => '');
         $hitung = count(array_intersect_key($data, $arrcheckpost));
         if($hitung == count($arrcheckpost)){
               $result = mysqli_query($mysqli, "INSERT INTO perkuliahan SET
               nim = '$data[nim]',
               kode_mk = '$data[kode_mk]',
               nilai = '$data[nilai]'");                
               if($result) {
                  $response=array(
                     'status' => 1,
                     'message' =>'Nilai Mahasiswa Added Successfully.'
                  );
               } else {
                  $response=array(
                     'status' => 0,
                     'message' =>'Mahasiswa Addition Failed.'
                  );
               }
         } else {
            $response=array(
                     'status' => 0,
                     'message' =>'Parameter Do Not Match'
                  );
         }
         header('Content-Type: application/json');
         echo json_encode($response);
      }
      
 
   function update_nilai_mhs($nim, $kode_mk) {
         global $mysqli;
         if(!empty($_POST["nim"]) && !empty($_POST["kode_mk"])) {
            $data=$_POST;
         } else {
            $data = json_decode(file_get_contents('php://input'), true);
         }

         $arrcheckpost = array('nim' => '','kode_mk' => '','nilai' => '');
         $hitung = count(array_intersect_key($data, $arrcheckpost));
         if($hitung == count($arrcheckpost)) {
            $nilai = $data['nilai'];
            
            $query = "UPDATE perkuliahan SET nilai = '$nilai' WHERE nim = '".$nim."' AND kode_mk = '".$kode_mk."'";
            $result = mysqli_query($mysqli, $query);
            if($result) {
               $response=array(
                  'status' => 1,
                  'message' =>'Mahasiswa Updated Successfully.'
               );
            } else {
               $response=array(
                  'status' => 0,
                  'message' =>'Mahasiswa Updation Failed.'
               );
            }
         } else {
            $response=array(
                     'status' => 0,
                     'message' =>'Parameter Do Not Match'
                  );
         }
         header('Content-Type: application/json');
         echo json_encode($response);
      }
 
   function delete_nilai_mhs($nim, $kode_mk) {
      global $mysqli;
      $query = "DELETE FROM perkuliahan WHERE nim='".$nim."' AND kode_mk='".$kode_mk."'";
      if(mysqli_query($mysqli, $query)) {
         $response=array(
            'status' => 1,
            'message' =>'Mahasiswa Deleted Successfully.'
         );
      }
      else {
         $response=array(
            'status' => 0,
            'message' =>'Nilai Mahasiswa Deletion Failed.'
         );
      }
      header('Content-Type: application/json');
      echo json_encode($response);
   }
?>