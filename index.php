<?php
$searchkey="";
$hasKeyWord=false;
if (isset($_GET['term'])){
    $searchkey=trim($_GET['term']);
    if(!empty($searchkey)){
        $hasKeyWord=true;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Image Search</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.css" type="text/css" /> 
  <script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
  <script type="text/javascript" src="http://code.jquery.com/ui/1.10.1/jquery-ui.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/masonry/3.3.2/masonry.pkgd.min.js"></script>
<style type="text/css">
.grid {
  padding: 20px;
  }
.grid-item {
  margin-bottom: 20px;
  float: left;
  width: 220px;
  }
  .box img {
  max-width: 100%
}
</style>
  <script type="text/javascript">
      function GetExtensionFileName(pathfilename)
      {
          return pathfilename.split('.').pop();
      }

      var oldkeyvalue = $(".auto").val();
      $(function() {
          //autocomplete
          $(".auto1").autocomplete({
                source: "search.php",
                minLength: 1,
                _renderMenu: function( ul, items ) {
                    var that = this;
                    $.each( items, function( index, item ) {
                        //that._renderItemData( ul, item );
                    });
                    //$( ul ).find( "li:odd" ).addClass( "odd" );
                },
                response: function( event, ui ) {alert(ui.content.length)}
          });
          var searchFunc = function(){
              keyvalue = $(".auto").val();
              if (oldkeyvalue==keyvalue) return;
              data={ term: keyvalue };
              $.ajax({
                  dataType: "json",
                  url: "search.php",
                  data: data,
                  success: function(data){
                      innerhtml="";
                      for(var i=0;i<data.length;i++){
                          ext = GetExtensionFileName(data[i].filename).toLowerCase();
                          innerhtml+="<div class='grid-item'>";
                          innerhtml+="<div>";
                          //innerhtml+="<a href='" + data[i].path.replace("/home/nfs/order-sys-share-data","file:///Z:") + "/" + data[i].filename + "' target='_new'>";
                          target="";
                          if(ext == "jpg" || ext == "png" || ext == "gif") {
                              //target=" target='_new'";
                          } 
                          innerhtml+="<a href='" + data[i].path.replace("/home/nfs/order-sys-share-data","originPic") + "/" + data[i].filename + "' download "+ target +">";
                          if(ext == "jpg" || ext == "png" || ext == "gif") {
                              innerhtml+="<img width=150 src='" + data[i].path.replace("/home/nfs/order-sys-share-data","pic") + "/" + data[i].filename + "' title=\"" + data[i].path.replace("/home/nfs/order-sys-share-data","") + data[i].filename + "\"/>";
                          }else{
                              iconF = "other.jpg";
                              if(ext=="ac6"){
                                  iconF = "ac6.jpg";
                              } else if (ext=="ai") {
                                  iconF = "ai.jpg";
                              } else if (ext=="cdr") {
                                  iconF = "cdr.jpg";
                              } else if (ext=="eps") {
                                  iconF = "eps.jpg";
                              } else if (ext=="ps") {
                                  iconF = "ps.jpg";
                              } else if (ext=="plt") {
                                  iconF = "plt.jpg";
                              } else if (ext=="jpg") {
                                  iconF = "jpg.jpg";
                              } else if (ext=="zip") {
                                  iconF = "zip.jpg";
                              } else if (ext=="ttf") {
                                  iconF = "ttf.jpg";
                              }
                              innerhtml+="<img width=150 src='./icon/" + iconF + "' title=\"" + data[i].path.replace("/home/nfs/order-sys-share-data","") + data[i].filename + "\" />";
                              
                          }
                          innerhtml+="</a>";
                          innerhtml+="<div style='width:150px;word-wrap:break-word;'><font size=2 color=red>" + ext + "</font><font size=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+data[i].filesize+"<br/>"+data[i].lastmod+"</font></div>";
                          innerhtml+="</div></div>";
                      }
                      $(".grid").html(innerhtml);
                      //console.log("result count:"+data.length);
                      //$('.grid').masonry('reload');
                      oldkeyvalue=keyvalue;
                  }
              });
          };
          $(".auto").keyup(searchFunc);
<?php
if ($hasKeyWord){
    echo "$(\".auto\").val('" . $searchkey . "');";
    echo "searchFunc();";
}
?>
      });
  </script>

</head>
<body> 

	<form action='' method='post'>
		<p><label> 请输入文件名:</label><input type='text' name='country' value='' class='auto'></p>
	</form>

<div class="grid js-masonry"
  data-masonry-options='{ "itemSelector": ".grid-item", "columnWidth": 150 }'>
</div>

</body>
</html>
