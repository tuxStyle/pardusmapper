<?php
declare(strict_types=1);
require_once('app/settings.php');

use Pardusmapper\Core\MySqlDB;
use Pardusmapper\Request;
use Pardusmapper\Post;
use Pardusmapper\Session;
use Pardusmapper\DB;

$dbClass = new MySqlDB(); // Create an instance of the Database class
$db = $dbClass->getDb();  // Get the mysqli connection object

// Set Univers Variable and Session Name
$uni = Request::uni();

if (is_null($uni)) { require_once(templates('lannding')); exit; }

$security = Session::security();
$url = Request::url();

session_name($uni);
session_start();

if (isset($_REQUEST['login'])) {
    if (0 === $security) {
        $name = Post::username();
        $pwd = Post::password();

        if ($debug) { echo $name . '<br>'; }
        if ($debug) { echo sha1($pwd) . '<br>'; }

        if (!isset($name) || !isset($pwd)) {
            session_destroy();
        } else {
            $u = DB::user(username: $name, universe: $uni);
            if ($debug) { xp($u); echo '<br>'; }
            if (is_null($u) || strcmp($u->password, sha1($pwd)) != 0) {
                session_destroy();
            } else {
                if ($debug) { echo 'Creating Session Variables<br>'; }
                session_regenerate_id(true);
                $_SESSION['user'] = $u->username;
                if ($u->user_id) { $_SESSION['id'] = $u->user_id; }
                if ($u->security) { $_SESSION['security'] = $u->security; }
                if ($u->login) { $_SESSION['login'] = $u->login; }
                if ($u->loaded) { $_SESSION['loaded'] = $u->loaded; }
                if ($u->faction) { $_SESSION['faction'] = $u->faction; }
                if ($u->syndicate) { $_SESSION['syndicate'] = $u->syndicate; }
                if ($u->rank) { $_SESSION['rank'] = $u->rank; }
                if ($u->comp) { $_SESSION['comp'] = $u->comp; }
                if ($u->imagepack) { setcookie("imagepack", $u->imagepack, time() + 60 * 60 * 24 * 365, "/"); }
                $dbClass->execute(sprintf('UPDATE %s_Users SET login = UTC_TIMESTAMP() WHERE LOWER(username) = ?', $uni), [
                    's', $name
                ]);
            }
        }
    }
    session_write_close();
    if ($debug) { xp($_SESSION); }
    if ($debug) { echo $url . '<br>'; }
    if (strpos($url, $base_url) === false) { $url = $base_url . '/' . $uni . '/index.php'; }
    if (!$debug) { header("Location: $url"); }
} else {
    $signedup = 0;
    $alreadysignedup = 0;
    $url = null;
    if (isset($_REQUEST['signedup'])) { $signedup = 1; }
    if (isset($_REQUEST['alreadysignedup'])) { $alreadysignedup = 1; }
    if (is_null($url)) { $url = $_SERVER['HTTP_REFERER']; }

    require_once(templates('login'));
}