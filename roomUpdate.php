<?php  include 'menu.php';
    require_once('config.php');
    if(!isset($_SESSION["sess_userid"])){
        echo '<script type="text/javascript">location.href = "index.php";</script>';
        echo '<script type="text/javascript">alert("please login");</script>';	
    }
?>
<link href="css/select2.min.css" rel="stylesheet" />
<script src="js/select2.min.js"></script>
<?php
    if(isset($_GET["roomId"])){
        $db = new PDO("mysql:dbname=".DBNAME.";host=".DBHOST, DBUSER, DBPASS);  
        $roomId = $db->quote($_GET["roomId"]);
        $query = $db->prepare("SELECT * FROM room where room_id=$roomId and status=1");
        $query->execute();
        $rooms=$query->fetchAll();
        $query = $db->prepare("SELECT feature_id FROM room_features where room_id=$roomId");
        $query->execute();
        $featuresSelected=$query->fetchAll();
    }
?>
<?php
if(isset($_POST['submit_image'])){
	$name = $_FILES['upload_file']['name'];
	list($txt, $ext) = explode(".", $name);
	$image_name = time().".".$ext;
	$tmp = $_FILES['upload_file']['tmp_name'];

	if(move_uploaded_file($tmp, 'uploads/'.$image_name)){
		$db = new PDO("mysql:dbname=".DBNAME.";host=".DBHOST, DBUSER, DBPASS);  
        $roomId = $db->quote($_GET["roomId"]);
        $roomImage = $db->quote('uploads/'.$image_name);
        $updateQuery =  $db->prepare("UPDATE room SET image_url=$roomImage WHERE room_id=$roomId");  
        $result = $updateQuery->execute();
        $rooms[0]["image_url"] = $roomImage;
    }
}


?>

<div class="search-page search-grid-full">
    <div class="booking">
        <div class="container">
            <div class="reservation-form">
                <div class="col-md-9 reservation-right">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="avatar-upload">
                            <div class="avatar-edit">
                                <input type='file' id="upload_file" name ="upload_file" accept=".png, .jpg, .jpeg" />
                                <label for="upload_file"></label>
                                <input type="submit" name='submit_image' value="Upload Image" id="submit_image"/>
                            </div>
                            <div class="avatar-preview">
                                <div id="imagePreview" style="background-image: url(<?php if ($rooms[0]["image_url"] == "") echo 'images/upload.png'; else  echo $rooms[0]["image_url"];?>);">
                                </div>
                            </div>
                        </div>
                    </form>
                    <form method="POST" action="roomUpdate.php?&city=<?php echo $_GET['city']?>&id=<?php echo $rooms[0]["room_id"]?>" name="custInfo" class="edit">
                        <h4>Description</h4>         			
                        <input type="text" name="desc" value="<?php echo $rooms[0]["room_desc"] ?>" placeholder="Description" required>
                        <h4>Price($)</h4>         			
                        <input type="number" name="price" value="<?php echo $rooms[0]["price"] ?>" placeholder="Price" required>
                        <h4>Room Type</h4>      
                        <input type="text" name="roomType" value="<?php echo $rooms[0]["room_type"] ?>" placeholder="Room Type" required>
                        <h4>Max occupancy</h4>         			
                        <input type="number" name="max_occupancy" value="<?php echo $rooms[0]["max_occupancy"] ?>" placeholder="Max Occupancy" required>
                        <h4>Features</h4>         			
                        <div class="sort-by">
                            <select class="sel" id="features" name="features[]" multiple="multiple">

                                <?php
                                    $db = new PDO("mysql:dbname=".DBNAME.";host=".DBHOST, DBUSER, DBPASS);  
                                    $query = $db->prepare("SELECT * FROM features");
                                    $query->execute();
                                    $rows=$query->fetchAll();
                                    for($i=0; $i< count($rows); $i++){
                                        $selected = false;
                                        for($j=0; $j< count($featuresSelected); $j++){
                                            echo $featuresSelected[$j]['feature_id']; 
                                            echo $rows[$i]['feature_id']; 
                                            if($featuresSelected[$j]["feature_id"] == $rows[$i]["feature_id"]){
                                                $selected = true;
                                            }
                                        }
                                        ?>
                                    <option value="<?php echo $rows[$i]["feature_id"]?>"
                                     <?php if($selected) echo 'selected'?>><?php echo $rows[$i]["feature_name"]?></option>  
                                    <?php }?>
                            </select>
                        </div><br/><br/>
                        <input type="submit" name="submitted" class="btn1 btn-1 btn-1e" value="UPDATE NOW">
                    </form>
                </div>
            </div>    
        </div>
    </div>
</div>



<script>
    $(document).ready(function() {
        $('#roomType').val('<?php echo $rooms[0]["room_type"]?>');
        $('#features').select2();
    });
    document.getElementById("upload_file").onchange = function() {
        document.getElementById("submit_image").click();
    };
</script>

<?php
    if(isset($_POST['submitted'])){
        editProfile();
    }
    function editProfile(){
        try{    
            $db = new PDO("mysql:dbname=".DBNAME.";host=".DBHOST, DBUSER, DBPASS);  
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
            $id = $db->quote($_GET["id"]);
            $desc = $db->quote($_POST["desc"]);
            $price = $db->quote($_POST["price"]);
            $roomType = $db->quote($_POST["roomType"]);
            $max_occupancy = $db->quote($_POST["max_occupancy"]);
            $updateQuery =  $db->prepare("UPDATE room SET room_desc=$desc, price=$price, room_type=$roomType, max_occupancy=$max_occupancy WHERE room_id=$id");  
            $result = $updateQuery->execute();
            $featuresList = $_POST["features"];
            $db = new PDO("mysql:dbname=".DBNAME.";host=".DBHOST, DBUSER, DBPASS);  
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
            $sql = "DELETE from room_features where room_id = $id";
            $db->exec($sql);
            $db = new PDO("mysql:dbname=".DBNAME.";host=".DBHOST, DBUSER, DBPASS);  
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
            for($featureNo = 0; $featureNo < count($featuresList); $featureNo++){
                $sql = "INSERT INTO room_features VALUES ($id, $featuresList[$featureNo])";
                $db->exec($sql);
            }
            $city= $_GET['city'];
            echo '<script type="text/javascript">location.href = "roomInfo.php?search=search&city='.$city.'";</script>';
            //header("Location:roomInfo.php");
        }catch(PDOException $ex){
            echo "<script type='text/javascript'>alert('$ex->getMessage();');</script>"; 
        }
    }
?>