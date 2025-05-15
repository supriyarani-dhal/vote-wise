<?php
//get all the elections from the database
$sql = "SELECT * FROM elections";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$elections = $stmt->fetchAll(PDO::FETCH_ASSOC);


//update the status according to the date

foreach ($elections as $election) {
    $starting_date = $election['starting_date'];
    $ending_date = $election['ending_date'];
    $curr_date = date('Y-m-d');
    $id = $election['id'];
    $status = $election['status'];

    if ($status == 'Ongoing') {
        $date_diff = date_diff(date_create($curr_date), date_create($ending_date));

        if ((int) $date_diff->format("%R%a") < 0) {
            $status = "Completed";
            $sql = "UPDATE elections SET status = :status WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':status' => $status,
                ':id' => $id
            ]);
        }
    } else {
        $date_diff = date_diff(date_create($curr_date), date_create($starting_date));

        if ((int) $date_diff->format("%R%a") <= 0) {
            $status = "Ongoing";
            $sql = "UPDATE elections SET status = :status WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':status' => $status,
                ':id' => $id
            ]);
        }
    }
}
?>

<div class="row my-3">
    <div class="col-12">
        <h3>All Elections</h3>
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
                    foreach ($elections as $row):
                        $election_id = $row['id'];
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['election_topic']) ?></td>
                            <td><?= htmlspecialchars($row['no_of_candidates']) ?></td>
                            <td><?= htmlspecialchars($row['starting_date']) ?></td>
                            <td><?= htmlspecialchars($row['ending_date']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td>
                                <a href="index.php?viewResults=<?= $election_id ?>" class="btn btn-success btn-sm">View
                                    Results</a>
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