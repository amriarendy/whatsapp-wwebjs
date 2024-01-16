<?php
include_once("../../helpers/connection.php");
include_once("../../helpers/function.php");
$login = cekSession();
if ($login == 0) {
    toastr_set("error", "Forbiden: sorry you are not login!");
    redirect("../../login.php");
}

$email = $_SESSION['email'];
$level = $_SESSION['level'];

if (post("contact")) {
    csrf_validate();
    $contact_name = post("contact_name");
    $contact_number = post("contact_number");
    $contact_category = post("contact_category");
    $contact_type = post("contact_type");

    $count = countDB("contacts", "contact_name", $contact_name);

    if ($count == 0) {
        $query = mysqli_query($conn, "INSERT INTO contacts(`contact_name`, `contact_number`, `contact_type`, `contact_category`, `user_id`, `created_at`) 
        VALUES ('$contact_name', '$contact_number', '$contact_type', '$contact_category', '$email', '$timeZone')");
        // if (!$result) {
        //     die('Query Error : ' . mysqli_errno($conn) .
        //         ' - ' . mysqli_error($conn));
        // }
        toastr_set("success", "Success: Create data!");
        redirect('contact.php');
        exit;
    } else {
        toastr_set("error", "Aborted: Failed create data!");
        redirect('contact.php');
        exit;
    }
}

if (get("act") == "delete") {
    $id = get("id");

    $query = mysqli_query($conn, "DELETE FROM contacts WHERE id='$id'");
    toastr_set("success", "Success: Delete data!");
    redirect("contact.php");
}

if (get("act") == "delete_all") {
    $query = mysqli_query($conn, "DELETE FROM contacts WHERE user_id='$email'");
    toastr_set("success", "Success: Delete all data!");
    redirect("contact.php");
}

require_once('../templates/header.php');
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-phone"></i> <b>Master Contact</b></h1>
    </div>

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a class="btn btn-primary" data-toggle="modal" data-target="#addModal" title="Add Data"><b>Create</b></a>
            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropDownImportContact" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <b>Import Ecxel</b>
            </button>
            <div class="dropdown-menu animated--fade-in" aria-labelledby="dropDownImportContact">
                <a class="dropdown-item" data-toggle="modal" data-target="#importContact"><b>Import Contact</b></a>
                <a class="dropdown-item" data-toggle="modal" data-target="#importGroup"><b>Import Group ID</b></a>
            </div>
            <a class="btn btn-danger" href="contact.php?act=delete_all" onclick="return confirm('Hapus semua? Klik OK')" title="Delete All"><b><i class="fas fa-dumpster fa-sm text-white-50"></i> Delete All</b></a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Full Name</th>
                            <th>Number</th>
                            <th>Category</th>
                            <th>Type Contact</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>Full Name</th>
                            <th>Number</th>
                            <th>Category</th>
                            <th>Type Contact</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        $no = 1;
                        $email = $_SESSION['email'];
                        if ($_SESSION['level'] === "admin") {
                            $query = mysqli_query($conn, "SELECT * FROM contacts");
                        } elseif ($_SESSION['level'] === "user") {
                            $query = mysqli_query($conn, "SELECT * FROM contacts WHERE user_id='$email'");
                        }
                        while ($row = mysqli_fetch_assoc($query)) {
                            echo '<tr>';
                            echo '<td class="text-center">' . $no++ . '</td>';
                            echo '<td>' . $row['contact_name'] . '</td>';
                            echo '<td class="text-center">' . $row['contact_number'] . '</td>';
                            echo '<td class="text-center">' . $row['contact_category'] . '</td>';
                            echo '<td class="text-center">' . $row['contact_type'] . '</td>';
                            echo '<td class="text-center">
                                <a class="btn-circle btn-danger btn-sm" href="contact.php?act=delete&id=' . $row['id'] . '" title="Delete"><i class="fas fa-trash"></i></a>
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
                <h5 class="modal-title" id="exampleModalLabel"><b>Create Data</b></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <?= csrf_input(); ?>
                    <input type="hidden" name="contact" id="contact" value="contact">
                    <div class="form-group">
                        <label for="contact_name">Full Name <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="contact_name" name="contact_name" placeholder="Full name.." required />
                    </div>
                    <div class="form-group">
                        <label for="contact_number">Number or Group ID<span style="color: red;">*</span></label>
                        <input type="number" class="form-control" id="contact_number" name="contact_number" required />
                        <div class="form-check">
                            <input type="checkbox" name="numberCheckbox" class="form-check-input" id="numberCheckbox" checked>
                            <label class="form-check-label" for="numberCheckbox" id="labelNumber">Type number</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="contact_category">Category <span style="color: red;">*</span></label>
                        <select class="form-control" name="contact_category" id="contact_category" required>
                            <option value="uncategorized">Uncategorized</option>
                            <?php
                            $email = $_SESSION['email'];
                            if ($_SESSION['level'] === "admin") {
                                $query = mysqli_query($conn, "SELECT category FROM categories");
                            } elseif ($_SESSION['level'] === "user") {
                                $query = mysqli_query($conn, "SELECT category, user_id FROM categories WHERE user_id='$email'");
                            }
                            while ($row = mysqli_fetch_assoc($query)) {
                                echo '<option value="' . $row['category'] . '">' . $row['category'] . ' </option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="contact_type">Contact Type <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="contact_type" name="contact_type" value="PERSONAL" required readonly />
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

<!-- Import Personal-->
<div class="modal fade" id="importContact" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><b>Import Personal</b></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="../../lib/import-excel.php" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <?= csrf_input(); ?>
                    <input type="hidden" name="personal" id="personal" value="personal">
                    <div class="form-group">
                        <label for="id_data">Import <span style="color: red;">*</span></label>
                        <input type="file" class="form-control" id="file" name="file" required />
                    </div>
                    <div class="form-group">
                        <label for="contact_category">Category <span style="color: red;">*</span></label>
                        <select class="form-control" name="contact_category" id="contact_category" required>
                            <option value="uncategorized" selected>Uncategorized</option>
                            <?php
                            $query = mysqli_query($conn, "SELECT * FROM categories");
                            while ($row = mysqli_fetch_assoc($query)) {
                                echo '<option value="' . $row['category'] . '">' . $row['category'] . ' </option>';
                            }
                            ?>
                        </select>
                    </div>
                    <p>Download Template <a href="<?php echo $base_url . 'public/files/Template Import Personal.xlsx' ?>" target="_blank">Disini</a></p>
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

<!-- Import Group-->
<div class="modal fade" id="importGroup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><b>Import Group</b></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="../../lib/import-excel.php" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <?= csrf_input(); ?>
                    <input type="hidden" name="group" id="group" value="group">
                    <div class="form-group">
                        <label for="id_data">Import <span style="color: red;">*</span></label>
                        <input type="file" class="form-control" id="file" name="file" required />
                    </div>
                    <div class="form-group">
                        <label for="contact_category">Category <span style="color: red;">*</span></label>
                        <select class="form-control" name="contact_category" id="contact_category" required>
                            <option value="uncategorized" selected>Uncategorized</option>
                            <?php
                            $query = mysqli_query($conn, "SELECT * FROM categories");
                            while ($row = mysqli_fetch_assoc($query)) {
                                echo '<option value="' . $row['category'] . '">' . $row['category'] . ' </option>';
                            }
                            ?>
                        </select>
                    </div>
                    <p>Download Template <a href="<?php echo $base_url . 'public/files/Template Import Group.xlsx' ?>" target="_blank">Disini</a></p>
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
    $(document).ready(function() {
        $('#numberCheckbox').click(function() {
            if ($(this).is(':checked')) {
                $('#contact_number').attr('type', 'number');
                $('#labelNumber').text("Type number contact +628xxxxx");
                $('#contact_type').val('PERSONAL');
            } else {
                $('#contact_number').attr('type', 'text');
                $('#labelNumber').text("Type Group ID Or Group Name");
                $('#contact_type').val('GROUP');
            }
        });
    });
</script>
</body>

</html>