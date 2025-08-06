<?php
require_once 'db.php';
require_once 'User.php';
require_once 'Note.php';

session_start();

$user = new User($db);
if (!$user->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$note = new Note($db);
$username = $_SESSION['username'];

$action = $_POST['action'] ?? null;
$title = $_POST['title'] ?? null;
$content = $_POST['content'] ?? null;
$noteId = $_POST['noteId'] ?? null;

if ($action === 'add' && $title && $content) {
    $note->createNote($username, $title, $content);
    header('Location: index.php');
    exit;
} elseif ($action === 'update' && $noteId && $title && $content) {
    $note->updateNote($noteId, $title, $content);
    header('Location: index.php');
    exit;
} elseif ($action === 'delete' && $noteId) {
    $note->deleteNote($noteId);
    header('Location: index.php');
    exit;
}


$notes = $note->loadNotesByUser($username);
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>NotizY</title>
    <link rel="stylesheet" href="css/style.css">
    <script type="module" src="js/script.js"></script>
</head>

<body>
    <header>
        <div class="header-content">
            <img src="images/logo.png" alt="Notizbuch Logo" class="logo">
            <h1>Hallo, <?php echo htmlspecialchars($username); ?></h1>
        </div>
        <form method="post" action="logout.php" class="logout-form">
            <button class="logout-button" type="submit">Abmelden</button>
        </form>
    </header>

    <div class="main-container">
        <div class="content-wrapper">
            <div id="noteForm" class="note-form-container">
                <h2 id="formHeader">Neue Notiz hinzufügen</h2>
                <form id="mainNoteForm" method="post" action="index.php">
                    <input type="hidden" name="action" value="add" id="formAction">
                    <input type="hidden" name="noteId" id="formNoteId">
                    <input type="text" name="title" id="title" required>
                    <textarea name="content" id="content" required></textarea>
                    <button type="submit" id="formSubmitButton">Notiz hinzufügen</button>
                </form>
            </div>

            <div class="notes-container">
                <h2>Deine Notizen</h2>
                <?php if (!empty($notes)): ?>
                    <?php foreach ($notes as $note): ?>
                        <div class="note">
                            <h3><?php echo htmlspecialchars($note['Titel']); ?></h3>
                            <p><?php echo nl2br(htmlspecialchars($note['Inhalt'])); ?></p>
                            <small><?php echo htmlspecialchars($note['Date']); ?></small>
                            <div class="note-actions">
                                <button type="button" onclick="editNote(
                                    '<?php echo $note['ID']; ?>',
                                    '<?php echo htmlspecialchars($note['Titel'], ENT_QUOTES); ?>',
                                    '<?php echo htmlspecialchars($note['Inhalt'], ENT_QUOTES); ?>'
                                )">Bearbeiten</button>
                                <form method="post" action="index.php" class="delete-note-form">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="noteId" value="<?php echo $note['ID']; ?>">
                                    <button type="submit">Löschen</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Keine Notizen vorhanden.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer>
        <p>NotizY © 2024</p>
    </footer>
</body>

</html>