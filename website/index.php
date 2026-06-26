<!DOCTYPE html>
<html>
<head>
    <title>Sistem Rekomendasi Film & Musik</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Sistem Rekomendasi Film & Musik</h1>

<form method="GET">
    <button type="submit" name="tampil">Tampilkan Film</button>
</form>

<?php

if(isset($_GET['tampil'])) {

    $query = '
    PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
    PREFIX ex: <http://example.org/film-musik#>

    SELECT ?film
    WHERE {
        ?film rdf:type ex:Film .
    }
    ';

    $url = "http://localhost:3030/filmdb/query?query=" . urlencode($query);

    $data = file_get_contents($url);

    $result = json_decode($data, true);

    echo "<h2>Daftar Film</h2>";

    echo "<ul>";

    foreach($result['results']['bindings'] as $item){

        $film = $item['film']['value'];

        $film = str_replace("http://example.org/film-musik#", "", $film);

        echo "<li>$film</li>";
    }

    echo "</ul>";
}
?>

</body>
</html>