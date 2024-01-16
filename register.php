<?php
require_once('helpers/connection.php');
require_once('helpers/function.php');

$picture = "https://source.unsplash.com/360x200?games";
$level = "user";
$session_attempt = "Disconnected";
$status = "actived";
$chunk = 100;
$api_key = md5(rand(100000, 999999));
$nameErr = $emailErr = $passErr = $phoneErr = $genderErr = "";
$name = $email = $pass = $phone = $gender = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (empty(post("name"))) {
		$nameErr = "Full name is required";
	} else {
		$name = validation_trim(post("name"));
		// check if name only contains letters and whitespace
		if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
			$nameErr = "Only letters and white space allowed";
		}
	}
	if (empty(post("email"))) {
		$emailErr = "Email is required";
	} else {
		$email = validation_trim(post("email"));
		// check if e-mail address is well-formed
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$emailErr = "Invalid email format";
		} else {
			$query = mysqli_query($conn, "SELECT email FROM users WHERE email='$email'");
			$row = mysqli_fetch_assoc($query);
			if ($row['email'] === $email) {
				$emailErr = "Email has been used!";
			}
		}
	}
	if (empty(post("password"))) {
		$passErr = "Password is required";
	} else {
		if (post("password") !== post("confirm_password")) {
			$passErr = "Password not same";
		}
		$password = sha1(post("password"));
	}
	if (empty(post("phone"))) {
		$phoneErr = "Phone is required";
	} else {
		$phone = validation_trim(post("phone"));
		if (!is_numeric($phone)) {
			$phoneErr = "Invalid numeric";
		}
	}
	if (empty(post("gender"))) {
		$genderErr = "Gender is required";
	} else {
		$gender = validation_trim(post("gender"));
	}
	if (post("_token")) {
		csrf_validate();
		$count = countDB("users", "email", $email);
		if ($count == 0) {
			$query = mysqli_query($conn, "INSERT INTO users(`name`, `email`, `password`, `phone`, `gender`, `picture`, `level`, `session_attempt`, `status`, `chunk`, `api_key`, `created_at`) VALUES('$name', '$email', '$password', '$phone', '$gender', '$picture', '$level', '$session_attempt', '$status', '$chunk', '$api_key', '$timeZone')");
			if ($query) {
				toastr_set("success", "Create account success!");
				redirect("login.php");
				exit;
			} else {
				toastr_set("error", "Something error, account not created!");
				redirect("register.php");
				exit;
			}
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="Content-Type" charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Register Account - <?= getSingleValDB("seo", "id", "1", "title") ?></title>
	<meta name="author" content="<?= getSingleValDB("seo", "id", "1", "author") ?>" />
	<meta name="copyright" content="<?= getSingleValDB("seo", "id", "1", "copyright") ?>" />
	<meta name="image" content="<?= getSingleValDB("seo", "id", "1", "image") ?>">
	<meta name="robots" content="<?= getSingleValDB("seo", "id", "1", "indexing") ?>" />
	<meta name="googlebot" content="<?= getSingleValDB("seo", "id", "1", "indexing") ?>" />
	<link rel="icon" type="image/x-icon" href="<?= getSingleValDB("seo", "id", "1", "favicon") ?>">
	<link rel="canonical" content href="<?= getSingleValDB("seo", "id", "1", "url") ?>" />
	<!-- Custom fonts for this template-->
	<link href="<?= $base_url ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;400;600;700;800;900&display=swap" rel="stylesheet" />
	<!-- Custom styles for this template-->
	<link href="<?= $base_url ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
	<!-- Select2 Vendor CSS -->
	<link href="<?= $base_url ?>/assets/vendor/toastr/toastr.min.css" rel="stylesheet">
	<!-- Toastr Vendor -->
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

</head>

<body class="bg-gradient-black">

	<div class="container">

		<!-- Outer Row -->
		<div class="row justify-content-center">

			<div class="col-xl-5 col-lg-12 col-md-9">

				<div class="card o-hidden border-0 shadow-lg my-5">
					<div class="card-body p-0">
						<!-- Nested Row within Card Body -->
						<div class="p-5">
							<div class="text-center">
								<h1 class="h4 text-gray-900 mb-4 font-weight-bold">Create Yout Account</h1>
							</div>
							<form class="user" action="" method="post">
								<?= csrf_input() ?>
								<div class="form-group">
									<label class="font-weight-bold" for="name">Full Name</label>
									<input type="name" class="form-control" id="name" name="name" value="<?php echo $name; ?>" placeholder="Full Name" />
									<span class="text-danger font-weight-bold"><?= $nameErr ?></span>
								</div>
								<div class="form-group">
									<label class="font-weight-bold" for="email">Email <b class="text-bold text-danger">*</b></label>
									<input type="text" class="form-control" id="email" name="email" value="<?php echo $email; ?>" placeholder="Actived Email" />
									<span class="text-danger font-weight-bold"><?= $emailErr ?></span>
								</div>
								<div class="form-group">
									<label class="font-weight-bold" for="password">Password <b class="text-bold text-danger">*</b></label>
									<input type="password" class="form-control" id="password" name="password" value="" placeholder="Password" />
									<span class="text-danger font-weight-bold"><?= $passErr ?></span>
								</div>
								<div class="form-group">
									<label class="font-weight-bold" for="confirm_password">Confirm Password <b class="text-bold text-danger">*</b></label>
									<input type="password" class="form-control" id="confirm_password" name="confirm_password" value="" placeholder="Confirm Password" />
									<span class="text-danger font-weight-bold"><?= $passErr ?></span>
								</div>
								<div class="form-group">
									<label class="font-weight-bold" for="phone">Phone Number</label>
									<input type="text" class="form-control" id="phone" name="phone" value="<?php echo $phone; ?>" placeholder="Format number: 628 or 082" />
									<span class="text-danger font-weight-bold"><?= $phoneErr ?></span>
								</div>
								<div class="form-group">
									<label class="font-weight-bold" for="phone">Gender</label>
									<select name="gender" id="gender" class="form-control">
										<option value="<?php echo $gender; ?>" selected><?php echo $gender; ?></option>
										<option value="male">Male</option>
										<option value="female">Female</option>
									</select>
									<span class="text-danger font-weight-bold"><?= $genderErr ?></span>
								</div>
								<button type="submit" class="btn btn-success btn-block font-weight-bold">
									Create Account
								</button>
								<hr>
							</form>
							<div class="text-center">
								<a class="small" href="login.php">Have account? login!</a>
							</div>
						</div>
					</div>
				</div>

			</div>

		</div>

	</div>
	<!-- Bootstrap core JavaScript-->
	<script src="./assets/vendor/jquery/jquery.min.js"></script>
	<script src="./assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
	<!-- Core plugin JavaScript-->
	<script src="./assets/vendor/jquery-easing/jquery.easing.min.js"></script>
	<!-- Custom scripts for all pages-->
	<script src="./assets/js/sb-admin-2.min.js"></script>
	<!-- Toastr -->
	<script src="./assets/vendor/toastr/toastr.min.js"></script>
</body>

</html>