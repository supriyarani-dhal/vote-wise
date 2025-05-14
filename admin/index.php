<?php
include 'inc/header.php';
include 'inc/navigation.php';

if (isset($_GET['home_page'])) {
    include 'inc/home.php';
} else if (isset($_GET['add_election'])) {
    include 'inc/addElection.php';
} else if (isset($_GET['add_candidate'])) {
    include 'inc/addCandidate.php';
} else if (isset($_GET['viewResults'])) {
    include 'inc/viewResults.php';
}
?>

<?php
include 'inc/footer.php';
?>