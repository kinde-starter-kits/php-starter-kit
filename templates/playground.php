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
                <form method="post" enctype="multipart/form-data">
                    <div class="d-flex flex-col items-start mb-1">
                        <div>
                            <label for="">Get Flag</label>
                            <label for=""> - Type</label>
                            <select name="flag_type" id="flag_type">
                                <option value="s">String</option>
                                <option value="i">Integer</option>
                                <option value="b">Boolean</option>
                            </select>
                        </div>
                        <div>
                            <div style="display: grid;grid-template-columns: 1fr 1fr 60px;gap: 10px;">
                                <input class="w-100 input" type="text" placeholder="Fill the flag" name="flag_input" id="flag_input" required>
                                <input class="w-100 input" type="text" name="flag_input_default" placeholder="Fill the default value" id="flag_input_default" required>
                                <a class="btn btn-light" id="flag" type="button">Get</a>
                            </div>
                        </div>
                        <div id="flag_text"></div>
                    </div>
                    <div class="d-flex flex-col items-start mb-1">
                        <label for="">Get Boolean Flag</label>

                        <div>
                            <div style="display: grid;grid-template-columns: 1fr 1fr 60px;gap: 10px;">
                                <input class="w-100 input" type="text" name="flag_boolean_input" placeholder="Fill the flag" id="flag_boolean_input" required>
                                <input class="w-100 input" type="text" name="flag_boolean_input_default" placeholder="Fill the default value" id="flag_boolean_input_default" required>
                                <a class="btn btn-light" id="flag_boolean" type="button">Get</a>
                            </div>
                        </div>
                        <div id="flag_boolean_text"></div>
                    </div>
                    <div class="d-flex flex-col items-start mb-1">
                        <label for="">Get Integer Flag</label>

                        <div>
                            <div style="display: grid;grid-template-columns: 1fr 1fr 60px;gap: 10px;">
                                <input class="w-100 input" type="text" name="flag_integer_input" id="flag_integer_input" placeholder="Fill the flag" required>
                                <input class="w-100 input" type="text" name="flag_integer_input_default" placeholder="Fill the default value" id="flag_integer_input_default" required>
                                <a class="btn btn-light" id="flag_integer" type="button">Get</a>
                            </div>
                        </div>
                        <div id="flag_integer_text"></div>
                    </div>
                    <div class="d-flex flex-col items-start mb-1">
                        <label for="">Get String Flag</label>

                        <div>
                            <div style="display: grid;grid-template-columns: 1fr 1fr 60px;gap: 10px;">
                                <input class="w-100 input" type="text" name="flag_string_input" id="flag_string_input" placeholder="Fill the flag" required>
                                <input class="w-100 input" type="text" name="flag_string_input_default" placeholder="Fill the default value" id="flag_string_input_default" required>
                                <a class="btn btn-light" id="flag_string" type="button">Get</a>
                            </div>
                        </div>
                        <div id="flag_string_text"></div>
                    </div>
                </form>
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
    <script>
        function getFlag(body) {
            var form_data = new FormData();
            for (var key in body) {
                form_data.append(key, body[key]);
            }
            const flagTypeEl = document.getElementById('flag_type');
            if (flagTypeEl) {
                form_data.append('flag_type', flagTypeEl.value);
            }
            fetch('/playground', {
                method: 'POST',
                body: form_data,
            }).then(async (response) => {
                const data = await response.text();

                const textEl = document.getElementById(`${body.type}_text`);
                    if (textEl) {
                        textEl.innerText = data
                    }
            }).catch((err) => {
                alert(err.message)
            })
        }
        ['flag', 'flag_boolean', 'flag_integer', 'flag_string'].forEach(e => {
            const domEl = document.getElementById(e);
            if (domEl) {
                domEl.addEventListener('click', function() {
                    const inputEl = document.getElementById(`${e}_input`);
                    if (inputEl?.value) {
                        const inputDefaultEl = document.getElementById(`${e}_input_default`);
                        getFlag({
                            type: e,
                            value: inputEl?.value,
                            default: inputDefaultEl?.value || ""
                        });
                    }
                })
            }
        })
    </script>
</body>

</html>