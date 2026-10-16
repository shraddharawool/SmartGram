<?php

/*
|--------------------------------------------------------------------------
| Smart Gram Database Connection
|--------------------------------------------------------------------------
*/

$host = "localhost";
$username = "root";
$password = "";
$database = "smartgram";


/*
|--------------------------------------------------------------------------
| MySQL Connection
|--------------------------------------------------------------------------
*/

mysqli_report(MYSQLI_REPORT_OFF);

$conn = new mysqli(
    $host,
    $username,
    $password,
    $database
);


/*
|--------------------------------------------------------------------------
| Database Connection Check
|--------------------------------------------------------------------------
*/

if ($conn->connect_error) {

    error_log(
        "Smart Gram Database Error: " . $conn->connect_error
    );

    die("
        <!DOCTYPE html>

        <html lang='en'>

        <head>

            <meta charset='UTF-8'>

            <meta
                name='viewport'
                content='width=device-width, initial-scale=1.0'
            >

            <title>Smart Gram - Connection Error</title>

            <style>

                body {
                    font-family: Arial, sans-serif;
                    background: #f5f7f6;
                    margin: 0;
                    padding: 20px;
                }

                .error-box {
                    max-width: 600px;
                    margin: 100px auto;
                    background: #ffffff;
                    padding: 35px;
                    text-align: center;
                    border-radius: 12px;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
                }

                h1 {
                    color: #087f5b;
                    margin-bottom: 15px;
                }

                p {
                    color: #555;
                    line-height: 1.6;
                }

                button {
                    background: #087f5b;
                    color: white;
                    border: none;
                    padding: 11px 24px;
                    border-radius: 6px;
                    cursor: pointer;
                    font-weight: bold;
                }

                button:hover {
                    background: #066b4d;
                }

            </style>

        </head>

        <body>

            <div class='error-box'>

                <h1>Smart Gram</h1>

                <h2>Connection Temporarily Unavailable</h2>

                <p>
                    Smart Gram is currently unable to connect
                    to the database.
                </p>

                <p>
                    Please check the server connection and try again.
                </p>

                <button onclick='location.reload()'>
                    Retry
                </button>

            </div>

        </body>

        </html>
    ");

}


/*
|--------------------------------------------------------------------------
| UTF-8 Support
|--------------------------------------------------------------------------
*/

if (!$conn->set_charset("utf8mb4")) {

    error_log(
        "Smart Gram Charset Error: " . $conn->error
    );

}

?>