<?php
    function createResizedCopy($sourceFile, $targetFile, $type, $crop = true, $width = 150, $height = 100) {
        $image = imagecreatefromstring(file_get_contents($sourceFile));

        $widthOrig = imagesx($image);
        $heightOrig = imagesy($image);
        $scaledHeight = ceil($heightOrig * ($width / $widthOrig));

        if (!$crop) {
            $height = $scaledHeight;
            $topMarginSource = 0;
        } else {
            $topMarginSource = floor(max(0, ($scaledHeight - $height) / $scaledHeight) * $heightOrig * 0.5);
        }

        $frameHeight = floor($heightOrig - 2 * $topMarginSource);
        $resized = imagecreatetruecolor($width, $height);

        // Keep transparency
        if ($type == 'png' && $specs['keepTransparency']) {
            $color = imagecolorallocatealpha($resized, 0, 0, 0, 127);
            imagefill($resized, 0, 0, $color);
            imagesavealpha($resized, TRUE);
            imagecolortransparent($resized, $color);
        // or set black background
        } else {
            $color = imagecolorallocate($resized, 0, 0, 0);
            imagefill($resized, 0, 0, $color);
        }

        imagecopyresized(
            $resized, // Put stuff into this image,
            $image, // take it from this image,
            0, 0, // put it at these target coords,
            0, $topMarginSource, // take it from these source coords,
            $width, $height, // put it with these dimensions,
            $widthOrig, $frameHeight // and take it from these dimensions
        );

        switch ($type) {
            case 'jpeg':
            case 'jpg':
                imagejpeg($resized, $targetFile, 90);
                break;
            case 'png':
                imagepng($resized, $targetFile, 9);
                break;
            case 'gif':
                imagegif($resized, $targetFile);
                break;
        }
    }

    ini_set("display_errors", true);
    error_reporting(E_ALL);

    const MAX_FILE_UPLOAD_NR = 3;
    const MAX_UPLOAD_SIZE_IN_MB = 5.0;

    $targetDir = "../assets/uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    if (!is_dir($targetDir . "thumbnails/")) {
        mkdir($targetDir . "thumbnails/", 0777, true);
    }
    if (!is_dir($targetDir . "main/")) {
        mkdir($targetDir . "main/", 0777, true);
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
        $type = array_search(
            $finfo->file($_FILES[$elementName]['tmp_name']),
            array(
                'jpg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
            ),
            true
        );
        if ($type === false) {
            $errors[$elementName][] = "Only JPG/JPEG, PNG & GIF files are allowed.";
            continue;
        }

        // Move uploaded file to target dir
        $newName = preg_replace("#[^a-zA-Z0-9\._-]#", "", basename($_FILES[$elementName]["name"]));
        if (!move_uploaded_file($_FILES[$elementName]["tmp_name"], $targetDir . $newName)) {
            $errors[$elementName][] = "There was an error uploading your file.";
            continue;
        }

        // Make main image
        createResizedCopy(
            $targetDir . $newName,
            $targetDir . "main/" . $newName,
            $type,
            false,
            1920,
            1280
        );

        // Make thumbnail
        createResizedCopy(
            $targetDir . "main/" . $newName,
            $targetDir . "thumbnails/" . $newName,
            $type,
            true,
            150,
            100
        );
    }

    // Delete images
    foreach ($_POST as $elementName => $val) {
        if (substr($elementName, 0, 2) == 'c_') {
            $filename = explode('_', $elementName);
            if (count($filename) < 3) {
                continue;
            }

            array_shift($filename);
            $ext = $filename[0];
            array_shift($filename);
            $filename = implode('_', $filename);

            $filename = preg_replace("#[^a-zA-Z0-9\._-]#", "", $filename);
            $filename = str_replace('..', '', $filename);

            if (empty($filename)) {
                continue;
            }

            $filename .= '.' . $ext;

            if (file_exists($targetDir . $filename)) {
                unlink($targetDir . $filename);
            }

            if (file_exists($targetDir . "main/" . $filename)) {
                unlink($targetDir . "main/" . $filename);
            }

            if (file_exists($targetDir . "thumbnails/" . $filename)) {
                unlink($targetDir . "thumbnails/" . $filename);
            }
        }
    }
?>

<html>
    <head>
        <meta charset="utf-8"/>
        <title>Upload</title>
        <link rel="icon" type="image/png" href="../favicon.png"/>
        <link href="../styles/upload.css" rel="stylesheet" type="text/css"/>
    </head>

    <body>
        <h1>Upload</h1>
        <form action="../upload/index.php" method="post" id="uploadForm" enctype="multipart/form-data">
            <?php for ($i = 1; $i <= MAX_FILE_UPLOAD_NR; $i++): ?>
                <input type="file" name="file_<?php echo $i; ?>"/>
                <?php if (!empty($errors["file_" . $i])): ?>
                    <ul class="errors">
                        <?php foreach ($errors["file_" . $i] as $error): ?>
                            <li class="error"><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            <?php endfor;?>

            <input type="submit" name="submit"/>
        </form>

        <h1>Delete Pictures</h1>
        <form action="../upload/index.php" method="post" id="deleteForm">
            <?php foreach (glob("../assets/uploads/thumbnails/*") as $index => $thumb): ?>
                <?php $name = explode('.', basename($thumb)); ?>
                <input class="check" type="checkbox" name="c_<?php echo $name[1] . '_' . $name[0]; ?>"/>
                <img class="thumbnail" src="<?php echo $thumb; ?>" alt="" />
                <br/><br/>
            <?php endforeach; ?>
            <input type="submit" name="submit" />
        </form>
    </body>
</html>