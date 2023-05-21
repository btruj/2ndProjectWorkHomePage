<?php
function getCharactersFromAPI()
{
    $searchCharacter = isset($_GET['searchCharacter']) ? $_GET['searchCharacter'] : null;
    $params = ['apikey' => '4eda7fae5265f683474bbb6d8c041c3a', 'nameStartsWith' => $searchCharacter, 'modifiedSince' => '1969-12-31T19:00:00-0500', 'orderBy' => 'name'];

    $params_string = http_build_query($params);

    // Initialize cURL
    $ch = curl_init();

    // set url
    curl_setopt($ch, CURLOPT_URL, 'https://gateway.marvel.com:443/v1/public/characters?' . $params_string);

    // set method
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

    // return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // set headers
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Referer: developer.marvel.com',
    ]);

    // Get the data from the API
    $response = curl_exec($ch);

    // stop if fails
    if (!$response) {
        die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
    }

    // Close the cURL resource
    curl_close($ch);

    // Return the decoded response
    return json_decode($response);
}

$characters = getCharactersFromAPI()->data->results;

if ($searchCharacter) {
    $filteredCharacter = [];
    foreach ($characters as $character) {
        if (stripos($$character->name, $searchCharacter) !== false) {
            $filteredCharacter[] = $character;
        }
    }
    $characters = $filteredCharacter;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Marvel</title>
    <style>
        body,
        html {
            letter-spacing: 2px;
            background-color: #181818;
            color: #F5F5F5;
            font-family: 'Fakt Soft Pro';
        }

        h1 {
            width: fit-content;
            padding: 15px;
            font-size: 2em;
            font-weight: 700;
            text-align: center;
            margin: 0 auto;
            border: #FF3131 solid 1px;
            border-radius: 8px;
        }

        .card {
            box-shadow: 0px 0px 10px 2px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 1200px;
            margin: 50px auto;
            border-radius: 15px;

            gap: 5px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .data {
            box-shadow: 0px 0px 5px 2px rgba(0, 0, 0, 0.2);
            background-color: #404040;
            width: 266px;
            height: 400px;
            padding: 10px;
            border: #FF3131 solid 1px;
            border-radius: 8px;
            transition: background .2s ease-in;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .data:hover {
            background: #ED1D24;
        }

        .data span {
            font-size: 1.4em;
            margin-bottom: 10px;
        }

        .data img {
            width: 100%;
            height: 55%;
            object-fit: cover;
            border-radius: 8px;
            margin-top: -60px;
        }

        .data .last-span {
            margin: 10px 0 0;
        }

        label {
            font-weight: bold;
        }

        form {
            width: fit-content;
            padding: 15px;
            font-size: 1.4em;
            font-weight: 700;
            text-align: center;
            margin: 20px auto;
            border: #FF3131 solid 1px;
            border-radius: 8px;
        }

        select,
        input[type="text"] {
            padding: 5px;
            border: 1px solid #444;
            border-radius: 5px;
            background-color: #404040;
            color: #F5F5F5;
        }

        input[type="submit"] {
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #ED1D24;
            color: #F5F5F5;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #ff0000;
        }
    </style>
</head>
<body>
    <h1>Marvel Characters</h1>
    <form>
        <label for="searchCharacter">Search Character Name:</label>
        <input type="text" name="searchCharacter" placeholder="character">
        <input type="submit" value="Search">
    </form>
    <div class="card">
        <?php foreach ($characters as $character) : ?>
            <a href="character.php?id=<?php echo $character->id; ?>" style="text-decoration: none; color: inherit;">
                <div class="data">
                    <span><u>Name</u>:<br> <?php echo $character->name; ?></span>
                    <br>
                    <img src="<?php echo $character->thumbnail->path . '.' . $character->thumbnail->extension; ?>" alt="Character Thumbnail">
                    <?php
                    $dateChange = new DateTime($character->modified);
                    $newDate = $dateChange->format("Y-m-d");
                    echo "<span class='last-span'><u>Modified</u>:{$newDate}</span>";
                    ?>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</body>

</html>