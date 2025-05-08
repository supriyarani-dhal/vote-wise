<?php
if (isset($_GET['added'])) {
    echo "<div class=\"alert alert-success my-3\" role=\"alert\">
            The Candidate has been added successfully!
        </div>";
} else if (isset($_GET["size_error"])) {
    echo "<div class=\"alert alert-warning my-3\" role=\"alert\">
            File size is too large. Please upload a file less than 2MB.
        </div>";
} else if (isset($_GET["type_error"])) {
    echo "<div class=\"alert alert-warning my-3\" role=\"alert\">
            Invalid file type. Please upload jpg, jpeg, png or webp file.
        </div>";
} else if (isset($_GET["upload_error"])) {
    echo "<div class=\"alert alert-danger my-3\" role=\"alert\">
            File upload failed. Please try again.
        </div>";
}


//get all the elections from the database
$sql = "SELECT * FROM elections ORDER BY starting_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$elections = $stmt->fetchAll(PDO::FETCH_ASSOC);

//get all the candidates from the database
$sql = "SELECT * FROM candidates";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<div class="row my-3">
    <div class="col-3">
        <h3>Add New Candidate</h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <select name="election_id" required class="form-control">
                    <option value="">Select Election</option>
                    <?php
                    if (count($elections) > 0) {
                        foreach ($elections as $election) {
                            $allowed_candidates = $election['no_of_candidates'];
                            $sql = "SELECT * FROM candidates WHERE election_id = " . $election['id'];
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute();
                            $candidates_count = $stmt->rowCount();

                            if ($candidates_count < $allowed_candidates) {
                                ?>
                                <option value="<?= htmlspecialchars($election['id']) ?>">
                                    <?= htmlspecialchars($election['election_topic']) ?>
                                </option>
                            <?php
                            } else { ?>
                                <option value="<?= htmlspecialchars($election['id']) ?>" disabled>
                                    <?= htmlspecialchars($election['election_topic']) ?> (Full)
                                </option>
                            <?php
                            }
                        }
                    } else {
                        echo "<option value=''>Please add an election first</option>";
                    } ?>
                </select>
            </div>
            <div class="form-group">
                <input type="text" name="candidate_name" placeholder="Candidate Name" class="form-control" required />
            </div>
            <div class="form-group">
                <input type="file" name="candidate_logo" class="form-control" required />
            </div>
            <div class="form-group">
                <textarea name="candidate_bio" class="form-control" placeholder="Candidate Details"></textarea>
            </div>
            <input type="submit" value="Add Candidate" name="add_candidate_btn" class="btn btn-success" />
        </form>
    </div>

    <div class="col-9">
        <h3>Candidate Details</h3>
        <?php if (count($candidates) > 0) { ?>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">S.No</th>
                        <th scope="col">Symbol</th>
                        <th scope="col">Name</th>
                        <th scope="col">Details</th>
                        <th scope="col">Election</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($candidates as $row):
                        $sql = "SELECT election_topic FROM elections WHERE id = " . $row['election_id'];
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute();
                        $election_name = $stmt->fetch(PDO::FETCH_ASSOC);
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><img src="<?= $row['candidate_photo']; ?>" alt="Candidate Symbol" class="candidate_symbol" />
                            </td>
                            <td><?= htmlspecialchars($row['candidate_name']) ?></td>
                            <td style="width: 40%;"><?= htmlspecialchars($row['candidate_details']) ?></td>
                            <td>
                                <?= htmlspecialchars($election_name['election_topic']) ?>
                            </td>
                            <td>
                                <a href="#" class="btn btn-warning btn-sm">Edit</a>
                                <a href="#" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach;
                    ?>
                </tbody>
            </table>
        <?php } else {
            echo "<h3 class='text-center text-danger my-5'>
                    No Candidates have been added yet. Please add a candidate.
                </h3>";
        } ?>
    </div>
</div>


<?php
if (isset($_POST['add_candidate_btn'])) {
    $election_id = $_POST['election_id'];
    $candidate_name = $_POST['candidate_name'];
    $candidate_details = $_POST['candidate_bio'];
    $created_by = $_SESSION['username'];
    $created_date = date('Y-m-d H:i:s');

    try {
        // Handle file upload logic
        //target directory for the uploaded file
        $targeted_dir = "../assets/images/candidate_photos/";

        //final file name with random number genertaed by rand(1111111111, 9999999999) to avoid overwriting
        $candidate_photo = $targeted_dir . rand(1111111111, 9999999999) . $_FILES['candidate_logo']['name'];

        // Temporary file location (XAMPP stores it here during upload)
        $candidate_photo_temp_name = $_FILES['candidate_logo']['tmp_name'];

        $candidate_photo_type = strtolower(pathinfo($candidate_photo, PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
        $image_size = $_FILES['candidate_logo']['size'];

        if ($image_size < 2000000) {
            if (in_array($candidate_photo_type, $allowed_extensions)) {
                if (move_uploaded_file($candidate_photo_temp_name, $candidate_photo)) {
                    // Add candidate into database
                    $sql = "INSERT INTO candidates (election_id,candidate_name, candidate_photo, candidate_details,  created_by, created_date) VALUES (:election_id, :candidate_name, :candidate_photo, :candidate_details, :created_by, :created_date)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ":election_id" => $election_id,
                        ":candidate_name" => $candidate_name,
                        ":candidate_photo" => $candidate_photo,
                        ":candidate_details" => $candidate_details,
                        ":created_by" => $created_by,
                        ":created_date" => $created_date
                    ]);

                    echo "<script>location.assign(\"index.php?add_candidate=1&added=1\");</script>";
                } else {
                    echo "<script>location.assign(\"index.php?add_candidate=1&upload_error=1\");</script>";
                }
            } else {
                echo "<script>location.assign(\"index.php?add_candidate=1&type_error=1\");</script>";
            }
        } else { // 2MB
            echo "script>location.assign(\"index.php?add_candidate=1&size_error=1\");</script>";
        }

    } catch (\Throwable $th) {
        echo "" . $th->getMessage() . "";
    }

}

?>