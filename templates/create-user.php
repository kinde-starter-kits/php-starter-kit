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
            <h1><a href="/">KindAuth</a></h1>
        </nav>
    </header>
    <main>
        <div class="container">
            <div class="card content p-3">
                <?php if (empty($result)) : ?>
                    <form action="/save-user" method="post" enctype="multipart/form-data">
                        <div class="d-flex flex-col items-start mb-1">
                            <label for="given_name">Given name</label>
                            <input class="w-100 input" type="text" name="given_name" id="given_name" required>
                        </div>
                        <div class="d-flex flex-col items-start mb-1">
                            <label for="family_name">Family name</label>
                            <input class="w-100 input" type="text" name="family_name" id="family_name" required>
                        </div>
                        <div class="d-flex flex-col items-start mb-1">
                            <label for="email">Email</label>
                            <input class="w-100 input" type="email" name="email" id="email" required>
                        </div>
                        <div>
                            <input class="btn" type="submit" value="Submit">
                        </div>
                    </form>
                <?php else : ?>
                    <div>
                        <div class="">
                            <h2>Status: </h2>
                            <p> <?= $result->getCreated() ? 'Created' : 'Existed' ?></p>
                        </div>
                        <div class="">
                            <h2>User Identity:</h2>
                            <p><?= $result->getIdentities()[0]->getResult()->getIdentityId() ?? 'Null' ?></p>
                        </div>
                    </div>
                <?php endif; ?>
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