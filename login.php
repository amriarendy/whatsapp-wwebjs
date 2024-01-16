<?php
include_once("./helpers/connection.php");
include_once("./helpers/function.php");

$login = cekSession();
if ($login == 1) {
	redirect("./pages/dashboard.php");
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (empty(post("email"))) {
		$emailErr = "Email is required";
	} else {
		$email = validation_trim(post("email"));
		// check if e-mail address is well-formed
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$emailErr = "Invalid email format";
		}
	}
	if (empty(post("password"))) {
		$passErr = "Password is required";
	}
}
if (post("_token")) {
	csrf_validate();
	$email = post("email");
	$password = post("password");

	$login = login($email, $password);
	if ($login == true) {
		redirect("./pages/dashboard.php");
	} else {
		toastr_set("error", "Username or password wrong!");
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="Content-Type" charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Login - <?= getSingleValDB("seo", "id", "1", "title") ?></title>
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
	<!-- Datatables Vendor CSS -->
	<link href="<?= $base_url ?>/assets/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
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
								<h1 class="h4 text-gray-900 mb-4"><b>Login</b></h1>
							</div>
							<form class="user" method="POST">
								<?= csrf_input(); ?>
								<div class="form-group">
									<input type="email" class="form-control" id="exampleInputEmail" name="email" value="<?= $email ?>" placeholder="Email..">
									<span class="text-danger font-weight-bold"><?= $emailErr ?></span>
								</div>
								<div class="form-group">
									<input type="password" class="form-control" id="exampleInputPassword" name="password" placeholder="Password..">
									<span class="text-danger font-weight-bold"><?= $passErr ?></span>
								</div>
								<hr />
								<button type="submit" class="btn btn-primary btn-block font-weight-bold">
									Login
								</button>
							</form>
							<hr />
							<div class="text-center">
								<a class="small" href="#">Forgot Password?</a>
							</div>
							<div class="text-center">
								<a class="small font-weight-bold" href="register.php">Create an Account!</a>
							</div>
						</div>
					</div>
				</div>

			</div>

		</div>
	</div>
	<!-- Bootstrap core JavaScript-->
	<script src="<?= $base_url ?>/assets/vendor/jquery/jquery.min.js"></script>
	<script src="<?= $base_url ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
	<!-- Core plugin JavaScript-->
	<script src="<?= $base_url ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
	<!-- Custom scripts for all pages-->
	<script src="<?= $base_url ?>/assets/js/sb-admin-2.min.js"></script>
	<!-- <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> -->
	<script src="<?= $base_url ?>/assets/vendor/sweetalert2/sweetalert2.all.min.js"></script>
	<script src="<?= $base_url ?>/assets/vendor/toastr/toastr.min.js"></script>
	<script src="<?= $base_url ?>/>public/js/app/swal.js"></script>
	<script src="<?= $base_url ?>/assets/vendor/toastr/toastr.min.js"></script>
	<script>
		<?php
		toastr_show();
		?>
	</script>
</body>

</html>