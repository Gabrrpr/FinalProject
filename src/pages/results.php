<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}
require_once __DIR__ . '/../../templates/header.php';
require_once __DIR__ . '/../db.php';

$db = get_db();

// Get all ended elections
$elections = $db->prepare('SELECT * FROM elections WHERE status = "ended" ORDER BY end_time DESC');
$elections->execute();
$elections = $elections->fetchAll();

// Get all candidates for ended elections
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
// Get vote counts for each candidate
$vote_counts = [];
if ($election_ids) {
    $in = implode(',', array_fill(0, count($election_ids), '?'));
    $stmt = $db->prepare('SELECT candidate_id, COUNT(*) as votes FROM votes WHERE election_id IN (' . $in . ') GROUP BY candidate_id');
    $stmt->execute($election_ids);
    foreach ($stmt->fetchAll() as $row) {
        $vote_counts[$row['candidate_id']] = $row['votes'];
    }
}
?>
<h2>Election Results</h2>
<?php if ($elections): ?>
    <?php foreach ($elections as $el): ?>
        <div style="margin-bottom:2em;">
            <h3><?= htmlspecialchars($el['name']) ?></h3>
            <p><em><?= htmlspecialchars($el['start_time']) ?> to <?= htmlspecialchars($el['end_time']) ?></em></p>
            <u>Results:</u>
            <ul>
                <?php if (!empty($candidates_by_election[$el['id']])): ?>
                    <?php foreach ($candidates_by_election[$el['id']] as $c): ?>
                        <li>
                            <?= htmlspecialchars($c['name']) ?><?php if ($c['info']): ?> — <?= htmlspecialchars($c['info']) ?><?php endif; ?>:
                            <strong><?= $vote_counts[$c['id']] ?? 0 ?></strong> vote(s)
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li><em>No candidates for this election.</em></li>
                <?php endif; ?>
            </ul>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No ended elections to display results for yet.</p>
<?php endif; ?>
<?php
require_once __DIR__ . '/../../templates/footer.php';
