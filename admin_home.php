<?php 
include 'header.php';
include 'admin.php';


$_SESSION['admin'] = new admin($_SESSION['admin_id']);
?>

<div class="row"> <!-- Create a Format of Three Tabs with any that want to place with it -->

	<div class="col-md-3"><!-- THE LEFT TAB -->
	
    	</div>	
	
	<H3 id="status" align="center">
	<?php 
		if(!isset($_SESSION['acctype']))
		{
			if(isset( $_COOKIE[session_name()])) // destroy the cookie which stores the session id
			{
				setcookie( session_name(), "", time()-3600, "/");
			}
			$_SESSION = array();
			session_unset();
			session_destroy();
			header("location:home.php");
		} 
		echo "Welcome ".$_SESSION['admin']->fname." To the Frabman Rank Me Now!";
					
				
		?></H3>
	<!-- THE MIDDLE TAB -->    
	
	<!------Yserri MY SLIDE --------->
        <div class="slider">
            <div class="container">
                <div class="row">
                    <div class="span10 offset1">
                        <div class="flexslider">
                            <ul class="slides">
                            	
                                                          
                                <li data-thumb="assets/img/slider/3.png" data-thumbratio="50/10" >
                                    <img src="assets/img/slider/3.png" height="400" width="100">
                                    <p class="flex-caption">Five Star Rate All Day Every Day!!!</p>
                                </li>
                          <!--      <li data-thumb="assets/img/slider/4.png">
                                    <img src="assets/img/slider/4.png">
                                    <p class="flex-caption">Its All About the Exp!!!!</p>
                                </li>-->
                                <li data-thumb="assets/img/slider/5.png" data-thumbratio="50/10">
                                    <img src="assets/img/slider/5.png" height="400" width="100">
                                    <p class="flex-caption">The One the Only</p>
                                </li> 
                              <!--   <li data-thumb="assets/img/slider/5.png">
                                    <img src="assets/img/slider/5.png">
                                    <p class="flex-caption">One of the Generals!</p>
                                </li>  -->                           
                                 
                                
                                                              
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!------Yserri MY SLIDE --------->
      	
	</div>	
	<!-- THE RIGHT TAB -->
	
	</div>
		<!--Yserri Javascript -->
       <!-- <script src="assets/js/jquery-1.8.2.min.js"></script>
        <script src="assets/bootstrap/js/bootstrap.min.js"></script>
        <script src="assets/js/jquery.ui.map.min.js"></script>-->
        <script src="assets/js/jquery.flexslider.js"></script>
        <script src="assets/js/jquery.tweet.js"></script>
        <script src="assets/js/jflickrfeed.js"></script>
        <script src="http://maps.google.com/maps/api/js?sensor=true"></script>
        
        <script src="assets/js/jquery.quicksand.js"></script>
        <script src="assets/prettyPhoto/js/jquery.prettyPhoto.js"></script>
        <script src="assets/js/scripts.js"></script>
		<!--Yserri Javascript -->

<?php include 'footer.php';?>