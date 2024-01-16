<?php
include_once("../../helpers/connection.php");
include_once("../../helpers/function.php");
$login = cekSession();
if ($login == 0) {
    toastr_set("error", "You no have access!!");
    redirect("../../login.php");
}
if ($_SESSION['level'] !== 'admin') {
    toastr_set("error", "Forbiden Access!!");
    redirect("../dashboard.php");
}
$token = md5(rand(100000, 999999));
if (post("add")) {
    csrf_validate();
    $name = post("name");
    $email = post("email");
    $password = sha1(post("password"));
    $gender = post("gender");
    $phone = post("phone");
    $status = post("status");
    $api_key = $token;
    $picture = "https://source.unsplash.com/360x200?games";
    $level = post("level");
    $chunk = post("chunk");

    $count = countDB("users", "email", $email);

    if ($count == 0) {
        $query = mysqli_query($conn, "INSERT INTO users(`name`, `email`, `password`, `picture`, `gender`, `phone`, `status`, `api_key`, `level`, `chunk`, `created_at`)
        VALUES
        ('$name', '$email', '$password', '$picture', '$gender', '$phone', '$status', '$api_key', '$level', '$chunk', '$timeZone')");

        toastr_set("success", "Data Added!");
        redirect("users.php");
        exit;
    } else {
        toastr_set("error", "data no have added!");
        redirect("users.php");
        exit;
    }
}

if (post("edit")) {
    csrf_validate();
    $id = post("id");
    $name = post("name");
    $email = post("email");
    $gender = post("gender");
    $phone = post("phone");
    $status = post("status");
    $level = post("level");
    $chunk = post("chunk");
    $count = countDB("users", "id", $id);

    if ($id) {
        $query = mysqli_query(
            $conn,
            "UPDATE users SET 
            name = '$name',
            email = '$email',
            gender = '$gender',
            phone = '$phone',
            status = '$status',
            level = '$level',
            chunk = '$chunk', 
            updated_at = '$timeZone' WHERE id = $id"
        );
        toastr_set("success", "Success: Create data!");
        redirect("users.php");
        exit;
    } else {
        toastr_set("error", "Aborted: Failed create data!");
        redirect("users.php");
        exit;
    }
}

if (post("changePassword")) {
    csrf_validate();
    $id = post("id");
    $password = sha1(post("password"));

    $count = countDB("users", "id", $id);

    if ($id) {
        $query = mysqli_query(
            $conn,
            "UPDATE users SET 
            password = '$password',
            updated_at = '$timeZone' WHERE id = $id"
        );
        toastr_set("success", "Success: Updated password!");
        redirect("users.php");
        exit;
    } else {
        toastr_set("error", "Aborted: Failed update password!");
        redirect("users.php");
        exit;
    }
}

if (get("act") == "generateKey") {
    $id = get("id");
    mysqli_query($conn, "UPDATE users SET api_key='$token' WHERE id='$id'");
    toastr_set("success", "Success: Generate api key");
    redirect("users.php");
    exit;
}

if (get("act") == "delete") {
    $id = get("id");

    $query = mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
    toastr_set("success", "Success: Delete data!");
    redirect("users.php");
    exit;
}

require_once('../templates/header.php');
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-users"></i> <b>Master Users Management</b></h1>
    </div>

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a class="btn btn-primary" data-toggle="modal" data-target="#addModal" title="Add Data"><b>Create Users</b></a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Api Key</th>
                            <th>Level</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Api Key</th>
                            <th>Level</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = mysqli_query($conn, "SELECT * FROM users");
                        while ($row = mysqli_fetch_assoc($query)) {
                            echo '<tr>';
                            echo '<td class="text-center">' . $no++ . '</td>';
                            echo '<td>' . $row['name'] . '</td>';
                            echo '<td class="text-center">' . $row['email'] . '</td>';
                            echo '<td class="text-center">' . $row['api_key'] . '</td>';
                            echo '<td class="text-center">' . $row['level'] . '</td>';
                            echo '<td class="text-center">
                                <a id="dataEdit" class="btn-circle btn-warning btn-sm" data-toggle="modal" data-target="#editModal" title="Edit"
                                        data-id="' . $row['id'] . '"
                                        data-name="' . $row['name'] . '"
                                        data-email="' . $row['email'] . '"
                                        data-status="' . $row['status'] . '"
                                        data-gender="' . $row['gender'] . '"
                                        data-phone="' . $row['phone'] . '"
                                        data-api_key="' . $row['api_key'] . '"
                                        data-level="' . $row['level'] . '"
                                        data-chunk="' . $row['chunk'] . '"
                                        >
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a id="changePassword" class="btn-circle btn-secondary btn-sm" data-toggle="modal" data-target="#changePW" title="Change Password" data-id="' . $row['id'] . '"><i class="fas fa-key"></i></a>
                                <a id="changeKey" class="btn-circle btn-primary btn-sm" title="Change API KEY" href="users.php?act=generateKey&id=' . $row['id'] . '" onclick="return confirm(`Are you sure to change api key`)"><i class="fas fa-fire"></i></a>
                                <a class="btn-circle btn-danger btn-sm" href="users.php?act=delete&id=' . $row['id'] . '" onclick="return confirm(`Are you sure want delete this user`)" title="Delete"><i class="fas fa-trash"></i></a>
                            </td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

<!-- modal start -->
<!-- Add Modal-->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><b>Create User</b></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <?= csrf_input(); ?>
                    <input type="hidden" class="form-control" id="add" name="add" value="add" />
                    <div class="form-group">
                        <label for="name">Full Name <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" name="name" placeholder="Full name" required />
                    </div>
                    <div class="form-group">
                        <label for="email">Email <span style="color: red;">*</span></label>
                        <input type="email" class="form-control" name="email" placeholder="Email" required />
                    </div>
                    <div class="form-group">
                        <label for="password">Password <span style="color: red;">*</span></label>
                        <input type="password" class="form-control" name="password" placeholder="Password" required />
                    </div>
                    <div class="form-group">
                        <label for="email">Gender</label>
                        <select class="form-control" name="gender">
                            <option value="" selected>-- Choose Gender --</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="phone">Number Whatsapp <span style="color: red;">*</span></label>
                        <input type="number" class="form-control" name="phone" placeholder="phone: 628 or 08" required />
                    </div>
                    <div class="form-group">
                        <label for="level">Level <span style="color: red;">*</span></label>
                        <select class="form-control" name="level" required>
                            <option value="user" selected>Users</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Activation <span style="color: red;">*</span></label>
                        <select class="form-control" name="status" required>
                            <option value="actived" selected>Actived</option>
                            <option value="deactived">Not Actived</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="chunk">Chunk <span style="color: red;">*</span></label>
                        <input type="number" class="form-control" name="chunk" value="100" placeholder="Limit Sender" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal-->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><b>Edit User</b></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="edit" name="edit" value="edit" />
                    <input type="hidden" class="form-control" id="id" name="id" placeholder="ID" />
                    <?= csrf_input(); ?>
                    <div class="form-group">
                        <label for="name">Full Name <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" />
                    </div>
                    <div class="form-group">
                        <label for="email">Email <span style="color: red;">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email Actived" />
                    </div>
                    <div class="form-group">
                        <label for="email">Gender</label>
                        <select class="form-control" id="gender" name="gender">
                            <option value="" selected>-- Choose Gender --</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="phone">Number Whatsapp <span style="color: red;">*</span></label>
                        <input type="number" class="form-control" id="phone" name="phone" placeholder="phone: 628 or 08" />
                    </div>
                    <div class="form-group">
                        <label for="level">Level <span style="color: red;">*</span></label>
                        <select class="form-control" name="level" id="level">
                            <option value="user" selected>Users</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Activation <span style="color: red;">*</span></label>
                        <select class="form-control" name="status" id="status">
                            <option value="actived" selected>Actived</option>
                            <option value="deactived">Not Actived</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="chunk">Chunk <span style="color: red;">*</span></label>
                        <input type="number" class="form-control" id="chunk" name="chunk" value="100" placeholder="Limit sender" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- modal end -->

<!-- Edit Modal-->
<div class="modal fade" id="changePW" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><b>Change Password User</b></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="changePassword" name="changePassword" value="changePassword" />
                    <input type="hidden" class="form-control" id="idChange" name="id" placeholder="ID" />
                    <?= csrf_input(); ?>
                    <div class="form-group">
                        <label for="password">Change Password <span style="color: red;">*</span></label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Change Password" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- modal end -->

</div>
<!-- End of Main Content -->
<?php require_once('../templates/footer.php'); ?>
<script type="text/javascript" src="<?= $base_url ?>/assets/app/datatables.js"></script>
<!-- <script type="text/javascript" src="<?= $base_url ?>/assets/app/load/load.js"></script> -->
<script>
    $(document).ready(() => {
        $(document).on('click', "#dataEdit", function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var email = $(this).data('email');
            var status = $(this).data('status');
            var gender = $(this).data('gender');
            var phone = $(this).data('phone');
            var level = $(this).data('level');
            var chunk = $(this).data('chunk');
            $('#id').val(id);
            $('#name').val(name);
            $('#email').val(email);
            $('#status').val(status);
            $('#gender').val(gender);
            $('#phone').val(phone);
            $('#level').val(level);
            $('#chunk').val(chunk);
            $('#editModal').modal('show');
        });
    });

    $(document).ready(() => {
        $(document).on('click', "#changePassword", function() {
            var id = $(this).data('id');
            $('#idChange').val(id);
            $('#changePW').modal('show');
        });
    });
</script>
</body>

</html>