<?php
if (isset($_GET['added'])) {
    echo "<div class=\"alert alert-success my-3\" role=\"alert\">
            Election has been added successfully!
        </div>";
} else if (isset($_GET['deleteElection'])) {

    $election_id = $_GET['deleteElection'];
    $sql = 'DELETE FROM elections WHERE id = :election_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":election_id" => $election_id,
    ]);

    echo "<div class=\"alert alert-success my-3\" role=\"alert\">
            Election has been deleted successfully!
        </div>";
}


//get all the elections from the database
$sql = "SELECT * FROM elections ORDER BY starting_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$elections = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<div class="row my-3">
    <div class="col-4">
        <h3>Add New Election</h3>
        <form method="POST">
            <div class="form-group">
                <input type="text" name="election_topic" placeholder="Election Topic" class="form-control" required />
            </div>
            <div class="form-group">
                <input type="number" name="number_of_candidates" placeholder="No of Candidates" class="form-control"
                    required />
            </div>
            <div class="form-group">
                <input type="text" onfocus="this.type='Date'" name="starting_date" placeholder="Starting Date"
                    class="form-control" required />
            </div>
            <div class="form-group">
                <input type="text" onfocus="this.type='Date'" name="ending_date" placeholder="Ending Date"
                    class="form-control" required />
            </div>
            <input type="submit" value="Add Election" name="add_election_btn" class="btn btn-success" />
        </form>
    </div>

    <div class="col-8">
        <h3>Election Details</h3>
        <?php if (count($elections) > 0) { ?>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">S.No</th>
                        <th scope="col">Election Name</th>
                        <th scope="col"># Candidates</th>
                        <th scope="col">Starting on</th>
                        <th scope="col">Ending on</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($elections as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['election_topic']) ?></td>
                            <td><?= htmlspecialchars($row['no_of_candidates']) ?></td>
                            <td><?= htmlspecialchars($row['starting_date']) ?></td>
                            <td><?= htmlspecialchars($row['ending_date']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td>
                                <button href="#" class="btn btn-danger btn-sm"
                                    onClick="handleDelete(<?= $row['id'] ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach;
                    ?>
                </tbody>
            </table>
        <?php } else {
            echo "<h3 class='text-center text-danger my-5'>
                    No elections have been added yet.
                </h3>";
        } ?>
    </div>
</div>

<script>
    const handleDelete = (id) => {
        if (confirm("Are you sure you want to delete this election?")) {
            location.assign(`index.php?deleteElection=${id}`);
        }
    }
</script>

<?php
if (isset($_POST['add_election_btn'])) {
    $election_topic = $_POST['election_topic'];
    $no_of_candidates = $_POST['number_of_candidates'];
    $starting_date = $_POST['starting_date'];
    $ending_date = $_POST['ending_date'];
    $created_by = $_SESSION['username'];
    $created_date = date('Y-m-d H:i:s');

    $date_diff = date_diff(date_create($created_date), date_create($starting_date));

    if ((int) $date_diff->format("%R%a") > 0) {
        $status = "Upcoming";
    } else {
        $status = "Ongoing";
    }

    try {
        $sql = "INSERT INTO elections (election_topic, no_of_candidates, starting_date, ending_date,status, created_by, created_date) VALUES (:election_topic, :no_of_candidates, :starting_date, :ending_date, :status,:created_by, :created_date)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":election_topic" => $election_topic,
            ":no_of_candidates" => $no_of_candidates,
            ":starting_date" => $starting_date,
            ":ending_date" => $ending_date,
            ":status" => $status,
            ":created_by" => $created_by,
            ":created_date" => $created_date
        ]);

        echo "<script>location.assign(\"index.php?add_election=1&added=1\");</script>";
    } catch (\Throwable $th) {
        echo "" . $th->getMessage() . "";
    }

}

?>