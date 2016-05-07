<?php
    const MAX_FILE_UPLOAD_NR = 10;

    $targetDir = "assets/uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $errors = array();

    // We hardcode a limit here in case the user tried to sneak in more
    // form elements than we can realistically handle
    for ($i = 0; $i < MAX_FILE_UPLOAD_NR; $i++) {
        if (!isset($_FILES["file_$i"])) {
            break;
        }

        $errors["file_$i"] = array();

        // Check if image file is an actual image or fake image
        $check = getimagesize($_FILES["file_$i"]["tmp_name"]);
        if ($check === false)  {
            $errors["file_$i"][] = "File is not an image.";
            continue;
        }

        $targetFile = $targetDir . basename($_FILES["file_$i"]["name"]);
        // Check if file already exists
        if (file_exists($targetFile)) {
            $errors["file_$i"][] = "File already exists.";
            continue;
        }

        // Check file size
        if ($_FILES["file_$i"]["size"] > 2000000) {
            $errors["file_$i"][] = "File is too large (max 2MB).";
            continue;
        }

        // Allow certain file formats
        $imageFileType = pathinfo($targetFile, PATHINFO_EXTENSION);
        if (!in_array($imageFileType, array("jpg", "jpeg", "png", "gif"))) {
            $errors["file_$i"][] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            continue;
        }

        // Move uploaded file to target dir
        if (!move_uploaded_file($_FILES["file_$i"]["tmp_name"], $targetFile)) {
            $errors["file_$i"][] = "Sorry, there was an error uploading your file.";
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
                <input type="file" name="file_$i" id="file_$i"/>
                <?php if (!empty($errors["file_$i"])): ?>
                    <ul class="errors">
                        <?php foreach ($errors["file_$i"] as $error): ?>
                            <li class="error"><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            <?php endfor;?>
        </form>
    </body>
</html>