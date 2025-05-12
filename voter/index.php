<?php
include 'inc/header.php';
include 'inc/navigation.php';

if (isset($_GET['voteCasted'])) {
    echo "<div class=\"alert alert-success my-3\" role=\"alert\">
            Vote has been casted successfully!
        </div>";
} else if (isset($_GET["voteError"])) {
    echo "<div class=\"alert alert-danger my-3\" role=\"alert\">
            Vote casting failed. Please try again.
        </div>";
}
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

                            //fetching candidate Votes
                            $sql = "SELECT COUNT(*) as total_votes FROM votings WHERE candidate_id = :candidate_id";
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute([
                                ':candidate_id' => $candidateId
                            ]);
                            $votes = $stmt->fetch(PDO::FETCH_ASSOC);
                            $totalVotes = $votes['total_votes'];
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
                                    <?php echo $totalVotes; ?>
                                </td>
                                <td>

                                    <?php
                                    // Check if the user has already casted a vote
                                    $sql = "SELECT * FROM votings WHERE election_id = :election_id AND candidate_id = :candidate_id AND voter_id = :voter_id";
                                    $stmt = $pdo->prepare($sql);
                                    $stmt->execute([
                                        ':election_id' => $electionId,
                                        ':candidate_id' => $candidateId,
                                        ':voter_id' => $_SESSION['user_id']
                                    ]);
                                    $alreadyVote = $stmt->fetch(PDO::FETCH_ASSOC);
                                    if ($alreadyVote) {
                                        ?> <button class="btn btn-light" disabled><img src="../assets/images/voted.png"
                                                alt="" width="40"></button>
                                        <?php
                                    } else {
                                        ?>
                                        <button class="btn btn-primary"
                                            onclick="handleVote(<?php echo $electionId; ?>, <?php echo $candidateId; ?>, <?php echo $_SESSION['user_id']; ?>)">Vote</button>
                                        <?php
                                    }
                                    ?>
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

<script>
    const handleVote = (election_id, candidate_id, voter_id) => {
        const confirmation = confirm("Are you sure you want to vote for this candidate?");
        if (confirmation) {
            // Proceed with the vote
            $.ajax({
                type: 'POST',
                url: 'inc/ajax.php',
                data: {
                    election_id: election_id,
                    candidate_id: candidate_id,
                    voter_id: voter_id
                },
                success: function (response) {
                    location.assign("inde.php?voteCasted=1");
                },
                error: function (xhr, status, error) {
                    location.assign("index.php?voteError=1");
                }
            });
        }
    }
</script>

<?php
include 'inc/footer.php';
?>