<?php
include_once("../helpers/connection.php");
include_once("../helpers/function.php");
$login = cekSession();
if ($login == 0) {
    toastr_set("error", "You no have access!!");
    redirect("../login.php");
}

$email = $_SESSION['email'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
$users = mysqli_fetch_assoc($query);
$name = post("name");
$gender = post("gender");
$phone = post("phone");
$picture = "https://source.unsplash.com/360x200?games";
$password = sha1(post("password"));

if (post("email")) {
    csrf_validate();
    $query = mysqli_query(
        $conn,
        "UPDATE users SET 
        name = '$name',
        gender = '$gender',
        phone = '$phone',
        picture = '$picture',
        updated_at = '$timeZone' WHERE email = '$email'"
    );
    toastr_set("success", "Success: Updated bio data!");
    redirect("profile.php");
}

if (post("password")) {
    csrf_validate();
    $query = mysqli_query(
        $conn,
        "UPDATE users SET 
        password = '$password',
        updated_at = '$timeZone' WHERE email = '$email'"
    );
    toastr_set("success", "Success: Update password!");
    redirect("profile.php");
}

require_once('./templates/header.php');
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-user"></i><b> Profile</b> <?php echo $expiredToken ?></h1>
    </div>
    <div class="row">
        <div class="col-lg-3 col-md-12 col-sm-12">
            <!-- Profile -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">My Profile</h6>
                </div>
                <div class="card-body table-responsive-xl table-responsive-sm">
                    <div class="text-center">
                        <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 25rem;" src="<?php echo $users['picture'] ?>" alt="...">
                    </div>
                    <table class="table table-striped table-hover table-responsive-xl table-responsive-sm">
                        <tr>
                            <td class="font-weight">Email :</td>
                            <td><?php echo $users['email'] ?></td>
                        </tr>
                        <tr>
                            <td class="font-weight">Name :</td>
                            <td><?php echo $users['name'] ?></td>
                        </tr>
                        <tr>
                            <td class="font-weight">Gender :</td>
                            <td><?php echo $users['gender'] ?></td>
                        </tr>
                        <tr>
                            <td class="font-weight">Phone :</td>
                            <td><?php echo $users['phone'] ?></td>
                        </tr>
                        <tr>
                            <td class="font-weight">Join :</td>
                            <td><?php echo $users['created_at'] ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5 col-md-12 col-sm-12">
            <!-- Profile -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Change My Profile</h6>
                </div>
                <form action="" method="POST">
                    <div class="card-body">
                        <?= csrf_input(); ?>
                        <div class="form-group">
                            <label for="email">Email <span style="color: red;">*</span></label>
                            <input id="email" name="email" type="email" class="form-control" placeholder="Valid Email.." value="<?php echo $users['email'] ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="name">Name <span style="color: red;">*</span></label>
                            <input id="name" name="name" type="text" class="form-control" placeholder="Full Name.." value="<?php echo $users['name'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="id_data_add">Gender <span style="color: red;">*</span></label>
                            <select id="gener" name="gender" class="form-control">
                                <option value="<?php echo $users['gender'] ?>" selected><?php echo $users['gender'] ?></option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone <span style="color: red;">*</span></label>
                            <input id="phone" name="phone" type="number" class="form-control" placeholder="Phone Number.." value="<?php echo $users['phone'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="picture">My Picture <span style="color: red;">*</span></label>
                            <input id="picture" name="picture" type="file" class="form-control" disabled>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-4 col-md-12 col-sm-12">
            <!-- Profile -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Change Password & API KEY</h6>
                </div>
                <form action="" method="POST">
                    <div class="card-body">
                        <?= csrf_input(); ?>
                        <div class="form-group">
                            <label for="password">Change Password <span style="color: red;">Kosongkan password jika tidak mengganti password</span></label>
                            <input id="password" name="password" type="password" class="form-control" placeholder="Kosongkan jika tidak mengganti password" required>
                        </div>
                        <div class="form-group">
                            <label for="api_key">Your API KEY </label>
                            <input id="api_key" name="api_key" type="text" class="form-control" value="<?php echo $users['api_key'] ?>" readonly>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning mb-1"><i class="fas fa-key"></i> Change Password</button>
                        <button type="submit" class="btn btn-danger mb-1" onclick="copyApi()"><i class="fas fa-copy"></i> Copy API KEY</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->
</div>
<!-- End of Main Content -->
<?php require_once('./templates/footer.php'); ?>
<script type="text/javascript" src="<?= $base_url ?>/assets/app/datatables.js"></script>
<script>
    function copyApi() {
        var copyKey = document.getElementById("api_key");
        copyKey.select();
        copyKey.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyKey.value);
        alert("Copied the key: " + copyKey.value);
    }
</script>
</body>

</html>