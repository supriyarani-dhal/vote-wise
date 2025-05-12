<?php
include("../../admin/inc/config.php");

if (isset($_POST["election_id"]) && isset($_POST["candidate_id"]) && isset($_POST["voter_id"])) {
    $election_id = $_POST["election_id"];
    $candidate_id = $_POST["candidate_id"];
    $voter_id = $_POST["voter_id"];

    try {
        $sql = "INSERT INTO votings(election_id,voter_id, candidate_id, voting_date, voting_time) VALUES(:election_id, :voter_id, :candidate_id, :voting_date, :voting_time)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":election_id" => $election_id,
            ":candidate_id" => $candidate_id,
            ":voter_id" => $voter_id,
            ":voting_date" => date('Y-m-d'),
            ":voting_time" => date('H:i:s a')
        ]);

        echo "success";
    } catch (\Throwable $th) {
        echo 'Voting Error ' . $th->getMessage();
    }


}
?>