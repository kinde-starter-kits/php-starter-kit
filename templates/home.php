<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <header>
        <nav class="nav container">
            <h1>KindAuth</h1>
            <?php if (!$isAuthenticated) : ?>
                <div>
                    <a class="btn btn-ghost" href="/login" type="button">Sign In</a>
                    <a class="btn btn-dark" href="/register" type="button">Sign Up</a>
                </div>
            <?php else : ?>
                <div class="header-group">
                    <a class="btn btn-dark" href="/playground" type="button">Flag Playground</a>
                    <a class="btn btn-dark" href="/create-user" type="button">Create user</a>
                    <div class="avatar"> <?= $shortName ?></div>
                    <p class="username"><?= $fullName ?></p>
                    <a class="btn btn-dark" href="/logout" type="button">Logout</a>
                </div>
            <?php endif; ?>
        </nav>
    </header>
    <main>
        <div class="container">
            <div class="card content p-3">
                <p class="text-headline-1">Let's start authenticating with KindeAuth</p>
                <p class="text-headline-2 mb-1">Configure your app</p>
                <div>
                    <a class="btn btn-light btn-l" href="https://kinde.com/docs/developer-tools/php-sdk" type="button" target="_blank">Go to docs</a>
                </div>
            </div>
        </div>
    </main>
    <footer class="footer">
        <div class="container">
            <strong>KindAuth</strong>
            <p>
                <span>Visit our</span>
                <a class="text-link" href="https://kinde.com/docs" type="button" target="_blank">helper center</a>
            </p>
            <small>© 2022 KindeAuth, Inc. All rights reserved</small>
        </div>
    </footer>
</body>

</html>