<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'CAMAGRU' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <script>const CSRF_TOKEN = "<?= Session::csrfToken() ?>";</script>
</head>

<body class="<?= $bodyClass ?? '' ?>">

    <?php require __DIR__ . '/partials/header.php';?>

    <main class="camagru-main">
        <div class="container">
            <?php require $viewContent; ?>
        </div>
    </main>

    <?php require __DIR__ . '/partials/footer.php';?>

    <script src="/assets/js/main.js"></script>
</body>
</html>