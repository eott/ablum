<?php
    function getThumbnails() {
        return glob("assets/uploads/thumbnails/*");
    }

    function getFirstPicture() {
        $globs = array_filter(glob("assets/uploads/*"), 'is_file');
        return isset($globs[0]) ? $globs[0] : null;
    }
?>

<html>
    <head>
        <meta charset="utf-8"/>
        <title>My example album</title>
        <link rel="icon" type="image/png" href="favicon.png"/>
        <link href="styles/example.css" rel="stylesheet" type="text/css"/>
        <script src="https://code.jquery.com/jquery-2.2.3.min.js"
            integrity="sha256-a23g1Nt4dtEYOj7bR+vTu7+T8VP13humZFBJNIYoEJo="
            crossorigin="anonymous">
        </script>
        <script src="scripts/example.js"></script>
    </head>

    <body>
        <div id="thumbsSlider">
            <ul>
                <?php foreach (getThumbnails() as $thumb): ?>
                    <li>
                        <img class="thumbnail" src="<?php echo $thumb; ?>" alt="" />
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div id="pictureFrame">
            <div id="navPrevious">
                <div class="navPreviousIcon"></div>
            </div>
            <div id="navNext">
                <div class="navNextIcon"></div>
            </div>

            <img id="mainPicture" src="<?php echo getFirstPicture(); ?>" alt="" />
        </div>
    </body>
</html>