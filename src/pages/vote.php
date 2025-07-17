<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}
require_once __DIR__ . '/../../templates/header.php';
require_once __DIR__ . '/../db.php';

$db = get_db();
$errors = [];
$success = [];
$user_id = $_SESSION['user_id'];

// Handle vote submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vote_election_id'], $_POST['candidate_id'])) {
    $election_id = $_POST['vote_election_id'];
    $candidate_id = $_POST['candidate_id'];
    // Check if user already voted in this election
    $stmt = $db->prepare('SELECT id FROM votes WHERE user_id = ? AND election_id = ?');
    $stmt->execute([$user_id, $election_id]);
    if ($stmt->fetch()) {
        $errors[] = 'You have already voted in this election.';
    } else {
        // Check if election is ongoing
        $now = date('Y-m-d H:i:s');
        $stmt = $db->prepare('SELECT * FROM elections WHERE id = ? AND start_time <= ? AND end_time >= ? AND status = "ongoing"');
        $stmt->execute([$election_id, $now, $now]);
        $election = $stmt->fetch();
        if (!$election) {
            $errors[] = 'Election is not active.';
        } else {
            // Record vote
            $stmt = $db->prepare('INSERT INTO votes (user_id, election_id, candidate_id) VALUES (?, ?, ?)');
            if ($stmt->execute([$user_id, $election_id, $candidate_id])) {
                $success[] = 'Vote submitted successfully!';
            } else {
                $errors[] = 'Failed to submit vote.';
            }
        }
    }
}

// Update election statuses (optional: auto-update based on time)
$now = date('Y-m-d H:i:s');
$db->prepare('UPDATE elections SET status = "ongoing" WHERE start_time <= ? AND end_time >= ?')->execute([$now, $now]);
$db->prepare('UPDATE elections SET status = "ended" WHERE end_time < ?')->execute([$now]);

// Get all ongoing elections
$elections = $db->prepare('SELECT * FROM elections WHERE status = "ongoing" ORDER BY start_time ASC');
$elections->execute();
$elections = $elections->fetchAll();

// Get all votes by this user
$user_votes = $db->prepare('SELECT election_id FROM votes WHERE user_id = ?');
$user_votes->execute([$user_id]);
$voted_elections = array_column($user_votes->fetchAll(), 'election_id');

// Get all candidates for ongoing elections
$election_ids = array_column($elections, 'id');
$candidates_by_election = [];
if ($election_ids) {
    $in = implode(',', array_fill(0, count($election_ids), '?'));
    $stmt = $db->prepare('SELECT * FROM candidates WHERE election_id IN (' . $in . ') ORDER BY id');
    $stmt->execute($election_ids);
    foreach ($stmt->fetchAll() as $c) {
        $candidates_by_election[$c['election_id']][] = $c;
    }
}
?>
<h2>Vote</h2>
<?php if ($errors): ?>
    <ul style="color:red;">
        <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
    </ul>
<?php endif; ?>
<?php if ($success): ?>
    <ul style="color:green;">
        <?php foreach ($success as $s): ?><li><?= htmlspecialchars($s) ?></li><?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if ($elections): ?>
    <?php foreach ($elections as $el): ?>
        <div style="margin-bottom:2em;">
            <h3><?= htmlspecialchars($el['name']) ?></h3>
            <p><em><?= htmlspecialchars($el['start_time']) ?> to <?= htmlspecialchars($el['end_time']) ?></em></p>
            <?php if (in_array($el['id'], $voted_elections)): ?>
                <p style="color:green;">You have already voted in this election.</p>
            <?php else: ?>
                <form method="POST">
                    <input type="hidden" name="vote_election_id" value="<?= $el['id'] ?>">
                    <?php if (!empty($candidates_by_election[$el['id']])): ?>
                        <?php foreach ($candidates_by_election[$el['id']] as $c): ?>
                            <label><input type="radio" name="candidate_id" value="<?= $c['id'] ?>" required> <?= htmlspecialchars($c['name']) ?><?php if ($c['info']): ?> — <?= htmlspecialchars($c['info']) ?><?php endif; ?></label><br>
                        <?php endforeach; ?>
                        <button type="submit">Vote</button>
                    <?php else: ?>
                        <p><em>No candidates for this election.</em></p>
                    <?php endif; ?>
                </form>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No ongoing elections available for voting.</p>
<?php endif; ?>

<?php
require_once __DIR__ . '/../../templates/footer.php';
