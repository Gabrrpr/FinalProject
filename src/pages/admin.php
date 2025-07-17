<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}
if ($_SESSION['role'] !== 'admin') {
    header('Location: /dashboard');
    exit;
}
require_once __DIR__ . '/../../templates/header.php';
require_once __DIR__ . '/../db.php';

$db = get_db();
$errors = [];
$success = [];

// Handle new election creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_election'])) {
    $name = trim($_POST['election_name'] ?? '');
    $start = $_POST['start_time'] ?? '';
    $end = $_POST['end_time'] ?? '';
    if (!$name || !$start || !$end) {
        $errors[] = 'All election fields are required.';
    } elseif (strtotime($end) <= strtotime($start)) {
        $errors[] = 'End time must be after start time.';
    } else {
        $stmt = $db->prepare('INSERT INTO elections (name, start_time, end_time, status, created_by) VALUES (?, ?, ?, "upcoming", ?)');
        if ($stmt->execute([$name, $start, $end, $_SESSION['user_id']])) {
            $success[] = 'Election created successfully.';
        } else {
            $errors[] = 'Failed to create election.';
        }
    }
}

// Handle new candidate creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_candidate'])) {
    $election_id = $_POST['election_id'] ?? '';
    $candidate_name = trim($_POST['candidate_name'] ?? '');
    $candidate_info = trim($_POST['candidate_info'] ?? '');
    if (!$election_id || !$candidate_name) {
        $errors[] = 'Election and candidate name are required.';
    } else {
        $stmt = $db->prepare('INSERT INTO candidates (election_id, name, info) VALUES (?, ?, ?)');
        if ($stmt->execute([$election_id, $candidate_name, $candidate_info])) {
            $success[] = 'Candidate added successfully.';
        } else {
            $errors[] = 'Failed to add candidate.';
        }
    }
}

// Get all elections
$elections = $db->query('SELECT * FROM elections ORDER BY id DESC')->fetchAll();
// Get all candidates grouped by election
$candidates = $db->query('SELECT * FROM candidates ORDER BY election_id, id')->fetchAll();
$candidates_by_election = [];
foreach ($candidates as $c) {
    $candidates_by_election[$c['election_id']][] = $c;
}
?>
<h2>Admin Panel</h2>
<p>Welcome, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>! You are logged in as <strong>admin</strong>.</p>

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

<h3>Create New Election</h3>
<form method="POST">
    <input type="hidden" name="create_election" value="1">
    <label>Election Name: <input type="text" name="election_name" required></label><br>
    <label>Start Time: <input type="datetime-local" name="start_time" required></label><br>
    <label>End Time: <input type="datetime-local" name="end_time" required></label><br>
    <button type="submit">Create Election</button>
</form>

<h3>Add Candidate to Election</h3>
<form method="POST">
    <input type="hidden" name="add_candidate" value="1">
    <label>Election:
        <select name="election_id" required>
            <option value="">Select election</option>
            <?php foreach ($elections as $el): ?>
                <option value="<?= $el['id'] ?>"><?= htmlspecialchars($el['name']) ?> (<?= htmlspecialchars($el['start_time']) ?>)</option>
            <?php endforeach; ?>
        </select>
    </label><br>
    <label>Candidate Name: <input type="text" name="candidate_name" required></label><br>
    <label>Candidate Info: <textarea name="candidate_info"></textarea></label><br>
    <button type="submit">Add Candidate</button>
</form>

<h3>Current Elections and Candidates</h3>
<?php if ($elections): ?>
    <ul>
        <?php foreach ($elections as $el): ?>
            <li>
                <strong><?= htmlspecialchars($el['name']) ?></strong><br>
                <em><?= htmlspecialchars($el['start_time']) ?> to <?= htmlspecialchars($el['end_time']) ?> (<?= htmlspecialchars($el['status']) ?>)</em><br>
                <u>Candidates:</u>
                <ul>
                    <?php if (!empty($candidates_by_election[$el['id']])): ?>
                        <?php foreach ($candidates_by_election[$el['id']] as $c): ?>
                            <li><?= htmlspecialchars($c['name']) ?><?php if ($c['info']): ?> — <?= htmlspecialchars($c['info']) ?><?php endif; ?></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li><em>No candidates yet.</em></li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No elections created yet.</p>
<?php endif; ?>

<?php
require_once __DIR__ . '/../../templates/footer.php';
