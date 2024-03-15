<div class="menu">
            <ul>
                <li><a href="index.php">HOME</a></li>
                <li><a href="bedroom.php">BEDROOM</a></li>
                <li><a href="dining.php">DINING</a></li>
                <li><a href="#">ABOUT US</a></li>
                <li><a href="mycart.php">MY CART</a></li>
                <?php
                if(session_status() == PHP_SESSION_NONE)
                session_start();

                if (isset($_SESSION['name'])) {
                    // User is logged in
                    $uppercaseName = strtoupper($_SESSION['name']);
                    echo '<li><span class="username">' . $uppercaseName . '</span></li>';
                    echo '<li><img src="image2.jpg" alt="User Face" class="user-face"></li>';  // Add the image here
		    echo '<li><a href="logout.php">LOGOUT</a></li>';
                    
                } else {
                    // User is not logged in
                    echo '<li><a href="login.php">LOGIN</a></li>';
                }
                ?>
            </ul>
</div>