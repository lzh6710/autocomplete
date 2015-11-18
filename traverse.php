<?php
    define('ROOT', dirname(__FILE__));
    require(ROOT."/PicThumb.class.php");

    define('ORIGIN_PATH', "/home/nfs/order-sys-share-data");
    define('THUMB_PATH', "/home/nfs/order-sys-share-data-thumb");
    define('CRC_PATH', "/home/nfs/order-sys-share-data-crc");

    function file_crc($file) 
    { 
        $file_string = file_get_contents($file); 
        $crc = crc32($file_string); 
        return sprintf("%u", $crc); 
    } 

    function convert_image($filepath = '.') {
        $crc = str_replace(ORIGIN_PATH,CRC_PATH,$filepath);
        $checksum = file_crc($filepath);
        if(file_exists($crc)){
            if(file_get_contents($crc) == $checksum){
                return;
            }
        }else{
            $crc_dir = dirname($crc);
            if(!file_exists($crc_dir))
            	mkdir($crc_dir, 0777, true );
        }
        $param = array(
            'type' => 'fit',
            'width' => 200,
            'height' => 200,
        );
        $logfile = ROOT.'/PicThumb.log';
        $dest = str_replace(ORIGIN_PATH,THUMB_PATH,$filepath);
        $dest_dir = dirname($dest);
        if(!file_exists($dest_dir))
            mkdir($dest_dir, 0777, true );
        $source = $filepath;
        $obj = new PicThumb($logfile);
        $obj->set_config($param);
        //echo $source . "-->" .$dest . "\n";
        $flag = $obj->create_thumb($source, $dest);
        if($flag){
            file_put_contents($crc,$checksum);
        }else{
            echo "convert image failed. path:" . $filepath . "\n";
        }
    }

    function traverse($path = '.',$myfile) {
        $current_dir = opendir($path);    //opendir()返回一个目录句柄,失败返回false
        while(($file = readdir($current_dir)) !== false) {    //readdir()返回打开目录句柄中的一个条目
            $sub_dir = $path . DIRECTORY_SEPARATOR . $file;    //构建子目录路径
            if($file == '.' || $file == '..') {
                continue;
            } else if(is_dir($sub_dir)) {    //如果是目录,进行递归
                //echo 'Directory ' . $file . ':<br>';
                traverse($sub_dir,$myfile);
            } else {    //如果是文件,直接输出
                //insert($stmt,$path,$file);
                if(strrpos($file,"'")!== false){
                    $old_sub_dir=$sub_dir;
                    $file = str_replace("'","",$file);
                    $sub_dir = $path . DIRECTORY_SEPARATOR . $file;
                    rename($old_sub_dir,$sub_dir);
                }
                $ext=pathinfo($file, PATHINFO_EXTENSION);
                fwrite($myfile, '"' . $path . '","' . $file . "\",\"" . date ("Y/m/d H:i:s", filemtime($sub_dir)) . '","' . filesize($sub_dir) . "\"\n");
                //if($ext=="jpg" || $ext=="png" || $ext=="gif" || $ext=="bmp"){
                    //convert_image($sub_dir);
                //}
            }
        }
    }

    function multi_query($sqlStatements) {
        $dbHost = "localhost";
        $dbUser = "filesearch";
        $dbPass = "filesearch123";
        $dbName = "filesearch";
        $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
        if ($mysqli->connect_error) {
            echo "Error Occurred While Connection To DataBase<br/>";
        }
        $sqlResult = $mysqli->multi_query($sqlStatements);
        if($sqlResult == true) {
            echo "Successfully Inserted Records<br/>";
        } else {
            echo "Some Error Occured While Inserting Records\n" . $mysqli->error;
        }
        $mysqli->close();
    }
    $file_path = "/tmp/file_path.csv";
    $myfile = fopen($file_path, "w") or die("Unable to open file!");
    multi_query("drop table if exists file_path_tmp;create table file_path_tmp (path varchar(10000),filename varchar(300),lastmod datetime,filesize int) engine=innodb;");
    traverse(ORIGIN_PATH,$myfile);
    multi_query("load data infile '/tmp/file_path.csv' into table file_path_tmp character set utf8 fields terminated by ',' optionally enclosed by '\"' lines terminated by '\n';");
    multi_query("alter table file_path_tmp add index (path),add index (filename), add index (lastmod);");
    multi_query("drop table file_path;rename table file_path_tmp to file_path;");
    fclose($myfile);
?>
