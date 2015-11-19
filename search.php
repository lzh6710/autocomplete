<?php
define('DB_SERVER', 'localhost');
define('DB_USER', 'filesearch');
define('DB_PASSWORD', 'filesearch123');
define('DB_NAME', 'filesearch');

date_default_timezone_set("Etc/GMT+8");

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
	    
	    $stmt = $conn->prepare("select filename,path,date_add(lastmod,INTERVAL 16 HOUR) lastmod,filesize from (select a.filename,path,lastmod,filesize, b.max_lastmod,b.filename subfilename from (SELECT filename,path,lastmod,filesize FROM file_path WHERE filename LIKE :term limit 200) a left join (select filename,max(lastmod) max_lastmod from (SELECT SUBSTRING_INDEX(filename,'.',1) filename,lastmod FROM file_path WHERE filename LIKE :term limit 200) a group by filename) b on SUBSTRING_INDEX(a.filename,'.',1) = b.filename) c order by max_lastmod desc, subfilename");
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
