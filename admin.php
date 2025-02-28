<?php
session_start();

// Set the account status variable. You can change this dynamically based on your logic.
$account_status = 'active'; // Change this to 'active' to enable the form.
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>LearnAble Admin Login</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--===============================================================================================-->
	<link rel="shortcut icon" type="image/x-icon" href="https://rabbischools.com.ng/press/wp-content/uploads/2020/04/icon.jpg">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
	<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
	<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<!--===============================================================================================-->
</head>
<body>
	
	<div class="limiter">
		<div class="container-login100"
			<?php
				if ($account_status == 'locked') {
					echo "style=\"background-image: url('images/locked.jpg');\"";  // Change to locked background image
				} else {
					echo "style=\"background-image: url('images/banner.jpg');\"";  // Active status background image
				}
			?>>
			
			<div class="wrap-login100 p-t-30 p-b-50">
				<?php if ($account_status == 'locked'): ?>
					<span class="login100-form-title p-b-41" style="color: red;">
						Account Locked
					</span>
					<p style="text-align: center; color: red;">Your account has been locked. Please contact the administrator for further assistance.</p>
				<?php else: ?>
					<span class="login100-form-title p-b-41">
						Admin Account Login
					</span>
					<?php if (isset($_SESSION['messagef']) && $_SESSION['messagef']): ?>
						<h4>
							<?php
								printf('<b>%s</b>', $_SESSION['messagef']);
								unset($_SESSION['messagef']);
							?>
						</h4>
					<?php endif; ?>
					<form method="post" action="entadmin.php" autocomplete="off" class="login100-form validate-form p-b-33 p-t-5">
						<div class="wrap-input100 validate-input" data-validate = "Enter username">
							<input class="input100" type="text" name="aaname" placeholder="User name" autocomplete="off">
							<span class="focus-input100" data-placeholder="&#xe82a;"></span>
						</div>

						<div class="wrap-input100 validate-input" data-validate="Enter password">
							<input class="input100" type="password" name="aapwd" placeholder="Password" autocomplete="off">
							<span class="focus-input100" data-placeholder="&#xe80f;"></span>
						</div>

						<div class="container-login100-form-btn m-t-32">
							<button class="login100-form-btn" name="but_admn" id="but_admn">
								Login
							</button>
						</div>
					</form>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div id="dropDownSelect1"></div>

	<!--===============================================================================================-->
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
	<!--===============================================================================================-->
	<script src="vendor/animsition/js/animsition.min.js"></script>
	<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
	<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
	<!--===============================================================================================-->
	<script src="vendor/daterangepicker/moment.min.js"></script>
	<script src="vendor/daterangepicker/daterangepicker.js"></script>
	<!--===============================================================================================-->
	<script src="vendor/countdowntime/countdowntime.js"></script>
	<!--===============================================================================================-->
	<script src="js/main.js"></script>
</body>
</html>
