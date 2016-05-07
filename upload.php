<?php
    ini_set("display_errors", true);
    error_reporting(E_ALL);

    const MAX_FILE_UPLOAD_NR = 10;
    const MAX_UPLOAD_SIZE_IN_MB = 5.0;

    $targetDir = "assets/uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $errors = array();

    // We hardcode a limit here in case the user tried to sneak in more
    // form elements than we can realistically handle
    for ($i = 1; $i <= MAX_FILE_UPLOAD_NR; $i++) {
        $elementName = "file_" . $i;

        if (!isset($_FILES[$elementName]["tmp_name"]) || empty($_FILES[$elementName]["tmp_name"])) {
            break;
        }

        $errors[$elementName] = array();

        // Check if image file is an actual image or fake image
        $check = getimagesize($_FILES[$elementName]["tmp_name"]);
        if ($check === false)  {
            $errors[$elementName][] = "File is not an image.";
            continue;
        }

        // Check file size
        if ($_FILES[$elementName]["size"] > MAX_UPLOAD_SIZE_IN_MB * 1e6) {
            $errors[$elementName][] = "File is too large (max " . MAX_UPLOAD_SIZE_IN_MB . "MB).";
            continue;
        }

        // Allow certain file formats
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $ext = array_search(
            $finfo->file($_FILES[$elementName]['tmp_name']),
            array(
                'jpg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
            ),
            true
        );
        if ($ext === false) {
            $errors[$elementName][] = "Only JPG/JPEG, PNG & GIF files are allowed.";
            continue;
        }

        // Move uploaded file to target dir
        $targetFile = $targetDir . preg_replace("#[^a-zA-Z0-9\._-]#", "", basename($_FILES[$elementName]["name"]));
        if (!move_uploaded_file($_FILES[$elementName]["tmp_name"], $targetFile)) {
            $errors[$elementName][] = "There was an error uploading your file.";
            continue;
        }
    }
?>

<html>
    <head>
        <meta charset="utf-8"/>
        <title>Upload</title>
        <link rel="icon" type="image/png" href="favicon.png"/>
        <link href="styles/upload.css" rel="stylesheet" type="text/css"/>
    </head>

    <body>
        <form action="upload.php" method="post" id="uploadForm" enctype="multipart/form-data">
            <?php for ($i = 1; $i <= MAX_FILE_UPLOAD_NR; $i++): ?>
                <input type="file" name="file_<?php echo $i; ?>" id="file_<?php echo $i; ?>"/>
                <?php if (!empty($errors["file_" . $i])): ?>
                    <ul class="errors">
                        <?php foreach ($errors["file_" . $i] as $error): ?>
                            <li class="error"><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            <?php endfor;?>

            <input type="submit" name="submit" id="submit"/>
        </form>
    </body>
</html>