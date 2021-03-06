<?php include 'menu.php';
require_once('config.php');
session_start();
if(!isset($_SESSION["sess_userid"])){
	echo '<script type="text/javascript">location.href = "index.php";</script>';
	echo '<script type="text/javascript">alert("please login");</script>';	
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Comet Inn a Hotel Category Flat Bootstrap Responsive Website Template | Search :: PHP</title>
<!-- for-mobile-apps -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Comet Inn Responsive web template, Bootstrap Web Templates, Flat Web Templates, Andriod Compatible web template, 
Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyErricsson, Motorola web design" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false);
		function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- //for-mobile-apps -->
<link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all" />
<link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
<link rel="stylesheet" type="text/css" href="css/jquery-ui1.css">
<!-- js -->
<script src="js/jquery-1.11.1.min.js"></script>
<!-- //js -->
<link href='//fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic' rel='stylesheet' type='text/css'>
	<!-- start-smoth-scrolling -->
		<script type="text/javascript" src="js/move-top.js"></script>
		<script type="text/javascript" src="js/easing.js"></script>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$(".scroll").click(function(event){		
					event.preventDefault();
					$('html,body').animate({scrollTop:$(this.hash).offset().top},1000);
				});
			});
		</script>
	<!-- start-smoth-scrolling -->
</head>
<body>
<div class ="container">

     <div class="col-md-12 search-grid-right">
     <br/><br/>
     <h3 class="tittle"> <?php echo $_SESSION["sess_name"]?>'s Wishlist</h2>

        <?php 
            try{ 
                $db = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);  
                
                if ($db->connect_error) {
                    die("Connection failed: " . $db->connect_error);
                } 
                //user id set condition
                if(true){
                    $user_id = $_SESSION['sess_userid'];                        
                    
                    if(isset($_GET['delete'])){
                        $room_id = $_GET['room_id'];
                        $querydelete = "DELETE from wishlist where room_id='$room_id' && user_id='$user_id'";
                        if (mysqli_query($db, $querydelete)) {
                            echo "<script type='text/javascript'>alert('Room removed from your wishlist!');</script>"; 
                        } else {
                            //change this content
                            echo "Error: " . $query1 . "<br>" . mysqli_error($db);
                        }
                    }
                    
                    $query = "select count(*) as count from room r 
                        join wishlist w
                        on r.room_id = w.room_id and r.status =1 and w.user_id ='$user_id';";						
                
                    // Find out how many items are in the table
                    
                    $result = $db->query($query)->fetch_assoc();
                    $total = $result['count'][0];
                    $limit = 2;
                    if($total>$limit){
                        // How many items to list per page
                    
                
                    // How many pages will there be
                    $pages = ceil($total / $limit);
                    
                    // What page are we currently on?
                    $page = min($pages, filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, array(
                        'options' => array(
                            'default'   => 1,
                            'min_range' => 1,
                        ),
                    )));
                
                    // Calculate the offset for the query
                    $offset = ($page - 1)  * $limit;
                
                    // Some information to display to the user
                    $start = $offset + 1;
                    $end = min(($offset + $limit), $total);
                
                    // The "back" link
                    $prevlink = ($page > 1) ? '<a href="?page=1" title="First page">&laquo;</a> <a href="?page=' . ($page - 1) . '" title="Previous page">&lsaquo;</a>' : '<span class="disabled">&laquo;</span> <span class="disabled">&lsaquo;</span>';
                
                    // The "forward" link
                    $nextlink = ($page < $pages) ? '<a href="?page=' . ($page + 1) . '" title="Next page">&rsaquo;</a> <a href="?page=' . $pages . '" title="Last page">&raquo;</a>' : '<span class="disabled">&rsaquo;</span> <span class="disabled">&raquo;</span>';
                
                    // Display the paging information
                    echo '<div style="float:right" id="paging"><p>', $prevlink, ' Page ', $page, ' of ', $pages, ' pages, displaying ', $start, '-', $end, ' of ', $total, ' results ', $nextlink, ' </p></div><br><br>';
                
                    // Prepare the paged query
                    
                    $query = "select * from room r 
                                join wishlist w
                                    on r.room_id = w.room_id and r.status =1 and w.user_id ='$user_id'
                                        LIMIT $limit OFFSET $offset;";	
                    
                    $result1 = $db->query($query);
                    // Do we have any results?
                    if ($result1->num_rows > 0) {
                            while ($row = $result1->fetch_assoc()) { 
                                $room_id = $row['room_id'];
                                $wishlist_id = $row['wishlist_id']; ?>
                                <div class="hotel-rooms">
                                    <div class="hotel-left">
                                        <a href="single.php"><span class="glyphicon glyphicon-bed" aria-hidden="true"></span><?php echo $row['room_type'] ?></a>
                                        <p><?php echo $row['room_desc'] ?></p>
                                        <div class="hotel-left-grids">
                                            <div class="hotel-left-one">
                                                <a href="single.html"><img src="<?php echo $row['image_url'] ?>" alt="<?php echo $row['room_desc'] ?>" /></a>
                                            </div>
                                            <div class="hotel-left-two">
                                                <div class="rating text-left">
                                                    <span><img src ="images/st<?php echo $row['customer_rating']; ?>.png"</span>
                                                </div>
                                                <div class="rating text-left">
                                                <?php
                                                    
                                                    $query2 = "select f1.feature_name from room a inner join room_features b on a.room_id=b.room_id inner join features f1 on b.feature_id=f1.feature_id where room_id = '$room_id';";
                            
                                                ?>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="hotel-right text-right">
                                        <div>
                                            <a style="background:white" href='wishlist.php?room_id=<?php echo $room_id?>&delete=1'>
                                            <img id = "wishlistImg" src="images/wishlist2.png" onmouseover="this.src='images/wishlist1.png'" onmouseout="this.src='images/wishlist2.png'" title="Remove from wishlist" /></a>									
                                        </div>
                                        <h4><span><?php echo $row['price']+rand(2, 100) ?></span><?php echo "  ".$row['price']?></h4>
                                        <p>Best price</p>
                                        <a href="single.php?wishlist_id=<?php echo $wishlist_id?>&src=wishlist">Reserve Now</a>
                                    </div>
                                    <div class="clearfix"></div>
                                    
                                </div>
                                <?php
                            }
                            }
                            //closing 
                    }else{
                    $query = "select * from room r 
                        join wishlist w
                            on r.room_id = w.room_id and w.user_id ='$user_id';";	
                    
                    $result1 = $db->query($query);
                    // Do we have any results?
                    if ($result1->num_rows > 0) {
                        while ($row = $result1->fetch_assoc()) { 
                            $room_id = $row['room_id']; 
                            $wishlist_id = $row['wishlist_id'];?>
                            <div class="hotel-rooms">
                                <div class="hotel-left">
                                    <a href="single.php"><span class="glyphicon glyphicon-bed" aria-hidden="true"></span><?php echo $row['room_type'] ?></a>
                                    <p><?php echo $row['room_desc'] ?></p>
                                    <div class="hotel-left-grids">
                                        <div class="hotel-left-one">
                                            <a href="single.html"><img src="<?php echo $row['image_url'] ?>" alt="<?php echo $row['room_desc'] ?>" /></a>
                                        </div>
                                        <div class="hotel-left-two">
                                            <div class="rating text-left">
                                                <span><img src ="images/st<?php echo $row['customer_rating']; ?>.png"</span>
                                            </div>
                                            <div class="rating text-left">
                                            <?php
                                                
                                                $query2 = "select f1.feature_name from room a inner join room_features b on a.room_id=b.room_id inner join features f1 on b.feature_id=f1.feature_id where room_id = '$room_id';";
                        
                                            ?>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                                
                                <div class="hotel-right text-right">
                                    <div>
                                        <a style="background:white" href='wishlist.php?room_id=<?php echo $room_id?>&delete=1'>
                                        <img id = "wishlistImg" src="images/wishlist2.png" onmouseover="this.src='images/wishlist1.png'" onmouseout="this.src='images/wishlist2.png'" title="Remove from wishlist" /></a>									
                                    </div>
                                    <h4><span><?php echo $row['price']+rand(2, 100) ?></span><?php echo "  ".$row['price']?></h4>
                                    <p>Best price</p>
                                    <a href="single.php?wishlist_id=<?php echo $wishlist_id?>&src=wishlist">Reserve Now</a>
                                </div>
                                <div class="clearfix"></div>
                                
                            </div>
                            <?php
                            }
                        }else{
                            echo "WishList is Empty! Try adding a few rooms as your favorites. ";
                        }
                    }
                }
                } catch (Exception $e) {
                    echo '<p>', $e->getMessage(), '</p>';
                }
                ?>    
        </div>
</div>

<!--//search-->
<!--footer-->
<div class="footer">
		<div class="container">				 	
			<div class="col-md-3 ftr_navi ftr">
				<h3>NAVIGATION</h3>
				<ul>
					<li><a href="index.html">Home</a></li>
					<li><a href="about.html">About</a></li>
					<li><a href="booking.html">Booking</a></li>
					<li><a href="contact.html">Contact</a></li>
				</ul>
			</div>
			<div class="col-md-3 ftr_navi ftr">
					 <h3>FACILITIES</h3>
					 <ul>
						 <li><a href="#">Double bedrooms</a></li>
						 <li><a href="#">Single bedrooms</a></li>
						 <li><a href="#">Royal facilities</a></li>						
						 <li><a href="#">Connected rooms</a></li>
					 </ul>
			</div>
			<div class="col-md-3 ftr_navi ftr">
				<h3>GET IN TOUCH</h3>
				<ul>
					<li>6314.001, WPL,</li>
					<li>University of Texas Dallas</li>
					<li></li>
				</ul>
			</div>
			<div class="col-md-3 ftr-logo">
				<a href="index.html"><span class="glyphicon glyphicon-home" aria-hidden="true"></span>Comet Inn</a>
				<ul>
					<li><a href="#" class="f1"> </a></li>
					<li><a href="#" class="f2"> </a></li>
					<li><a href="#" class="f3"> </a></li>
				</ul>
			</div>
			<div class="clearfix"> </div>
		</div>
	</div>
<!--footer-->
<!-- for bootstrap working -->
	<script src="js/bootstrap.js"></script>
<!-- //for bootstrap working -->
<!-- smooth scrolling -->
	<script type="text/javascript">
		$(document).ready(function() {
		/*
			var defaults = {
			containerID: 'toTop', // fading element id
			containerHoverID: 'toTopHover', // fading element hover id
			scrollSpeed: 1200,
			easingType: 'linear' 
			};
		*/								
		$().UItoTop({ easingType: 'easeOutQuart' });
		});
	</script>
	<a href="#" id="toTop" style="display: block;"> <span id="toTopHover" style="opacity: 1;"> </span></a>
<!-- //smooth scrolling -->

</body>
</html>