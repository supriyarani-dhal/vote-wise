<?php
//get all the elections from the database
$sql = "SELECT * FROM elections ORDER BY starting_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$elections = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                    foreach ($elections as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['election_topic']) ?></td>
                            <td><?= htmlspecialchars($row['no_of_candidates']) ?></td>
                            <td><?= htmlspecialchars($row['starting_date']) ?></td>
                            <td><?= htmlspecialchars($row['ending_date']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td>
                                <a href="#" class="btn btn-success btn-sm">View Results</a>
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