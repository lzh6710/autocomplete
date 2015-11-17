<?php
define('DB_SERVER', 'localhost');
define('DB_USER', 'filesearch');
define('DB_PASSWORD', 'filesearch123');
define('DB_NAME', 'filesearch');


 function formatBytes($size) { 
  $units = array(' B', ' KB', ' MB', ' GB', ' TB'); 
  for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024; 
  return round($size, 2).$units[$i]; 
 }

if (isset($_GET['term'])){
     $return_arr = array();
     $searchkey=trim($_GET['term']);
     if(!empty($searchkey)){
	try {
	    $conn = new PDO("mysql:host=".DB_SERVER.";port=8889;dbname=".DB_NAME, DB_USER, DB_PASSWORD);
	    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    
	    $stmt = $conn->prepare('SELECT filename,path,lastmod,filesize FROM file_path WHERE filename LIKE :term order by lastmod desc limit 100');
	    $stmt->execute(array('term' => '%'.$searchkey.'%'));
	    
	    while($row = $stmt->fetch()) {
                $rownew = array();
                $rownew['filename'] = $row['filename'];
                $rownew['path'] = $row['path'];
                $rownew['lastmod'] = date ("Y/m/d H:i:s", strtotime($row['lastmod']));
                $rownew['filesize'] = formatBytes($row['filesize']);
	        $return_arr[] =  $rownew;
	    }

	} catch(PDOException $e) {
	    echo 'ERROR: ' . $e->getMessage();
	}
    }

    /* Toss back results as json encoded array. */
    echo json_encode($return_arr);
}


?>
