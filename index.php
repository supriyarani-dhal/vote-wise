<?php
require_once "admin/inc/config.php";
?>

<!DOCTYPE html>
<html>

<head>
	<title>Authentication- vote wise</title>
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/login.css">
	<link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
	<div class="container h-100">
		<div class="d-flex justify-content-center h-100">
			<div class="user_card" style="height: 450px;">
				<div class="d-flex justify-content-center">
					<div class="brand_logo_container">
						<img src="assets/images/logo.gif" class="brand_logo" alt="Logo">
					</div>
				</div>

				<?php
				if (isset($_GET['sign-up'])) {
					?>
					<div class="d-flex justify-content-center form_container">
						<form method="POST">
							<div class="input-group mb-2">
								<div class="input-group-append">
									<span class="input-group-text"><i class="fas fa-user"></i></span>
								</div>
								<input type="text" name="su_username" class="form-control input_user" placeholder="username"
									required>
							</div>
							<div class="input-group mb-2">
								<div class="input-group-append">
									<span class="input-group-text"><i class="fas fa-user"></i></span>
								</div>
								<input type="text" name="su_contact" class="form-control input_number"
									placeholder="contact number" required>
							</div>
							<div class="input-group mb-2">
								<div class="input-group-append">
									<span class="input-group-text"><i class="fas fa-key"></i></span>
								</div>
								<input type="password" name="su_password" class="form-control input_pass"
									placeholder="password" required>
							</div>
							<div class="input-group mb-2">
								<div class="input-group-append">
									<span class="input-group-text"><i class="fas fa-key"></i></span>
								</div>
								<input type="password" name="su_retype_password" class="form-control input_pass"
									placeholder="Re-enter password" required>
							</div>
							<div class="input-group mb-2">
								<div class="input-group-append">
									<span class="input-group-text"><i class="fas fa-key"></i></span>
								</div>
								<input type="text" name="su_user_role" class="form-control input_pass"
									placeholder="user role" required>
							</div>

							<div class="d-flex justify-content-center mt-4 login_container">
								<button type="submit" name="su_button" class="btn login_btn">Sign Up</button>
							</div>
						</form>
					</div>

					<div class="mt-3">
						<div class="d-flex justify-content-center links text-white">
							Already have an account? <a href="index.php" class="ml-2 text-white">Sign In</a>
						</div>
					</div>
					<?php
				} else {
					?>
					<div class="d-flex justify-content-center form_container">
						<form method="POST">
							<div class="input-group mb-3">
								<div class="input-group-append">
									<span class="input-group-text"><i class="fas fa-user"></i></span>
								</div>
								<input type="text" name="si_contact" class="form-control input_user"
									placeholder="contact number" required>
							</div>
							<div class="input-group mb-2">
								<div class="input-group-append">
									<span class="input-group-text"><i class="fas fa-key"></i></span>
								</div>
								<input type="password" name="si_password" class="form-control input_pass"
									placeholder="password" required>
							</div>
							<div class="form-group">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="customControlInline">
									<label class="custom-control-label text-white" for="customControlInline">Remember
										me</label>
								</div>
							</div>
							<div class="d-flex justify-content-center mt-5 login_container">
								<button type="submit" name="si_button" class="btn login_btn">Login</button>
							</div>
						</form>
					</div>

					<div class="mt-4">
						<div class="d-flex justify-content-center links text-white">
							Don't have an account? <a href="?sign-up=1" class="ml-2 text-white">Sign Up</a>
						</div>
						<div class="d-flex justify-content-center links">
							<a href="#" class="text-white">Forgot your password?</a>
						</div>
					</div>
					<?php
				}
				?>

				<?php
				if (isset($_GET['registered'])) {
					echo "<span class='text-success text-center'>✅ Registration successful!</span>";
				} else if (isset($_GET['invalid'])) {
					echo "<span class='text-danger text-center'>❌ Passwords does not match!</span>";
				} else if (isset($_GET['error'])) {
					echo "<span class='text-danger text-center'>❌ Registration failed!</span>";
				} else if (isset($_GET['not_found'])) {
					echo "<span class='text-warning text-center'>⚠ Invalid credentials! Please try again</span>";
				} else if (isset($_GET['not_registered'])) {
					echo "<span class='text-danger text-center'>❌ User not registered!</span>";
				}

				?>

			</div>
		</div>
	</div>
</body>

<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/jquery.min.js"></script>

</html>

<?php
require_once "admin/inc/config.php";
if (isset($_POST['su_button'])) {
	$su_username = $_POST['su_username'];
	$su_contact = $_POST['su_contact'];
	$su_password = $_POST['su_password'];
	$su_retype_password = $_POST['su_retype_password'];
	$user_role = $_POST['su_user_role'];

	// Hash the password for security
	$hashedPassword = password_hash($su_password, PASSWORD_DEFAULT);


	if ($su_password == $su_retype_password) {
		try {
			$sql = "INSERT INTO users(username, contact, password, user_role) VALUES(:username, :contact, :password, :user_role)";
			$stmt = $pdo->prepare($sql);
			$stmt->execute([
				":username" => $su_username,
				":contact" => $su_contact,
				":password" => $hashedPassword,
				":user_role" => $user_role
			]);

			echo "<script>location.assign(\"index.php?sign-up=1&registered=1\");</script>";

		} catch (PDOException $e) {
			echo "<script>location.assign(\"index.php?sign-up=1&error=1\");</script>";
		}

	} else {

		echo "<script>location.assign(\"index.php?sign-up=1&invalid=1\");</script>";

	}


} else if (isset($_POST['si_button'])) {
	$si_contact = $_POST['si_contact'];
	$si_password = $_POST['si_password'];

	try {
		$sql = "SELECT * FROM users WHERE contact = :contact";
		$stmt = $pdo->prepare($sql);
		$stmt->execute([
			":contact" => $si_contact,
		]);

		$user = $stmt->fetch();

		if (!$user) {
			echo "<script>location.assign(\"index.php?not_registered=1\");</script>";
		}

		if ($user && password_verify($si_password, $user['password'])) {
			session_start();

			$_SESSION['user_role'] = $user['user_role'];
			$_SESSION['username'] = $user['username'];
			$_SESSION['user_id'] = $user['id'];

			if ($user['user_role'] == "Admin") {
				$_SESSION['key'] = "AdminKey";
				echo "<script>location.assign(\"admin/index.php?home_page=1\");</script>";
			} else {
				$_SESSION['key'] = "VoterKey";
				echo "<script>location.assign(\"voter/index.php\");</script>";
			}

		} else {
			echo "<script>location.assign(\"index.php?not_found=1\");</script>";
		}
	} catch (PDOException $e) {
		echo "<script>location.assign(\"index.php?not_registered=1\");</script>";
	}
}
?>