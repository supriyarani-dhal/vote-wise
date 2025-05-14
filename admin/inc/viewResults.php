<div class="row my-3">
    <div class="col-12">
        <h3>ELECTION RESULTS</h3>

        <?php
        // getting the election id
        $electionId = $_GET['viewResults'];

        if ($electionId !== null) {
            // Fetch election details
            $sql = "SELECT * FROM elections WHERE id = :election_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ":election_id" => $electionId
            ]);
            $election = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            echo "No election ID provided.";
        }

        if ($election) {
            $electionTopic = htmlspecialchars($election['election_topic']);
            $election_status = htmlspecialchars($election['status']);

            ?>

            <!-- showing all the election details -->
            <table class="table">
                <thead>
                    <tr>
                        <th colspan="4" class="bg-green text-white">
                            <h5> ELECTION TOPIC: <?php echo strtoupper($electionTopic) . " (" . $election_status . ")" ?>
                            </h5>
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
                                $sql = "SELECT * FROM votings WHERE election_id = :election_id AND voter_id = :voter_id";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute([
                                    ':election_id' => $electionId,
                                    ':voter_id' => $_SESSION['user_id']
                                ]);
                                $alreadyVote = $stmt->fetch(PDO::FETCH_ASSOC);
                                if ($alreadyVote) {

                                    $voteToCandidate = $alreadyVote['candidate_id'];

                                    // Check if the user has already casted a vote for this candidate
                                    if ($voteToCandidate == $candidateId) {
                                        ?> <button class="btn btn-light" disabled><img src="../assets/images/voted.png"
                                                alt="" width="40"></button>
                                        <?php
                                    } else {
                                        ?> <button class="btn btn-primary btn-disabled" disabled> Voted </button>
                                        <?php
                                    }

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

            <!-- showing all the voter details -->
            <hr>
            <h3>Voting Details</h3>
            <table class="table mb-5">

                <?php
                //fetch all the votings
                $sql = 'SELECT * FROM votings WHERE election_id = :election_id';
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':election_id' => $electionId
                ]);
                $votings = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($votings) > 0) {
                    foreach ($votings as $vote) {
                        $vote_id = $vote['id'];
                        $voter_id = $vote['voter_id'];
                        $candidate_id = $vote['candidate_id'];

                        //fetching the voter name
                        $sql = 'SELECT * FROM users WHERE id = :voter_id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([
                            ':voter_id' => $voter_id
                        ]);
                        $voter = $stmt->fetch(PDO::FETCH_ASSOC);

                        if (count($voter) > 0) {
                            $voter_name = $voter['username'];
                            $voter_contact = $voter['contact'];
                        } else {
                            $voter_name = 'No_date';
                            $voter_contact = $voter['contact'];
                        }

                        //fetching the candidate name
                        $sql = 'SELECT * FROM candidates WHERE id = :candidate_id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([
                            ':candidate_id' => $candidate_id
                        ]);
                        $candidate = $stmt->fetch(PDO::FETCH_ASSOC);

                        if (count($candidate) > 0) {
                            $candidate_name = $candidate['candidate_name'];
                        } else {
                            $candidate_name = 'No_date';
                        }
                        ?>

                        <tr>
                            <th>Sl. NO</th>
                            <th>Voter name</th>
                            <th>Contact No</th>
                            <th>Voted To</th>
                            <th>Date</th>
                            <th>Time</th>
                        </tr>

                        <tr>
                            <td><?= htmlspecialchars($vote_id) ?></td>
                            <td><?= htmlspecialchars($voter_name) ?></td>
                            <td><?= htmlspecialchars($voter_contact) ?></td>
                            <td><?= htmlspecialchars($candidate_name) ?></td>
                            <td><?= htmlspecialchars($vote['voting_date']) ?></td>
                            <td><?= htmlspecialchars($vote['voting_time']) ?></td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </table>
            <?php


        } else {
            echo "<h3 class='text-center text-danger my-5'>
            No elections are created yet.
            </h3>";
        }
        ?>
    </div>
</div>