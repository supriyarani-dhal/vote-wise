<?php
include 'inc/header.php';
include 'inc/navigation.php';
?>

<div class="row my-3">
    <div class="col-12">
        <h3>VOTERS PANEL</h3>

        <?php
        // Fetch ongoing elections
        $sql = "SELECT * FROM elections WHERE status = 'ongoing'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $ongoingElections = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalActiveElections = count($ongoingElections);


        if ($totalActiveElections > 0) {
            foreach ($ongoingElections as $election) {
                $electionTopic = htmlspecialchars($election['election_topic']);
                $electionId = htmlspecialchars($election['id']);

                ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th colspan="4" class="bg-green text-white">
                                <h5> ELECTION TOPIC: <?= strtoupper($electionTopic) ?></h5>
                            </th>
                        </tr>
                        <tr>
                            <th> Photo </th>
                            <th> Candidate Details </th>
                            <th>No. of Votes </th>
                            <th> Action </th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $sql = "SELECT * FROM candidates WHERE election_id = :election_id";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([
                            ':election_id' => $electionId
                        ]);
                        $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($candidates as $candidate) {
                            $candidateId = $candidate['id'];
                            $candidatePhoto = $candidate['candidate_photo'];
                            ?>
                            <tr>
                                <td>
                                    <img src="<?= htmlspecialchars($candidatePhoto) ?>" alt="Candidate Photo" class="img-fluid"
                                        width="100">
                                </td>
                                <td>
                                    <h5><?= htmlspecialchars($candidate['candidate_name']) ?></h5>
                                    <p><?= htmlspecialchars($candidate['candidate_details']) ?></p>
                                </td>
                                <td>

                                </td>
                                <td>
                                    <a href="vote.php?election_id=<?= $electionId ?>&candidate_id=<?= $candidateId ?>"
                                        class="btn btn-primary">Vote</a>
                                </td>
                            </tr>
                            <?php
                        }

                        ?>
                    </tbody>
                </table>
                <?php
            }

        } else {
            echo "<h3 class='text-center text-danger my-5'>
            No elections are currently ongoing.
            </h3>";
        }
        ?>
    </div>
</div>

<?php
include 'inc/footer.php';
?>