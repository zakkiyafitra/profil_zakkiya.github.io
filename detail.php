<!DOCTYPE html>
<html>
<head>
    <title>Detail Data</title>

    <style>

        body{
            background:#0f172a;
            color:white;
            font-family:Arial;
            margin:0;
            padding:0;
        }

        .container{
            width:80%;
            margin:auto;
            padding:50px;
        }

        .card{
            background:#1e293b;
            border-radius:20px;
            padding:40px;
            box-shadow:0 4px 10px rgba(0,0,0,0.3);
        }

        .icon{
            font-size:100px;
            text-align:center;
            margin-bottom:20px;
        }

        h1{
            text-align:center;
            margin-bottom:40px;
        }

        .detail{
            margin-bottom:20px;
            padding:18px;
            background:#334155;
            border-radius:12px;
            font-size:18px;
        }

        .label{
            color:#60a5fa;
            font-weight:bold;
        }

        a{
            display:inline-block;
            margin-top:30px;
            padding:12px 24px;
            background:#2563eb;
            color:white;
            text-decoration:none;
            border-radius:10px;
            transition:0.3s;
        }

        a:hover{
            background:#1d4ed8;
        }

    </style>

</head>
<body>

<div class="container">

<?php

if(isset($_GET['film'])){

    $nama = $_GET['film'];

    // decode URL pencarian
    $nama = urldecode($nama);

    $url = "http://localhost:3030/filmdb/query";

    $query = "

    PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
    PREFIX ex: <http://example.org/film-musik#>

    SELECT ?type ?genre ?tahun ?rating ?sutradara ?penyanyi
    WHERE {

        OPTIONAL {
            ex:$nama rdf:type ?type .
        }

        OPTIONAL {
            ex:$nama ex:memilikiGenre ?genre .
        }

        OPTIONAL {
            ex:$nama ex:tahunRilis ?tahun .
        }

        OPTIONAL {
            ex:$nama ex:rating ?rating .
        }

        OPTIONAL {
            ex:$nama ex:disutradaraiOleh ?sutradara .
        }

        OPTIONAL {
            ex:$nama ex:dinyanyikanOleh ?penyanyi .
        }

    }

    ";

    $data = http_build_query([
        "query" => $query
    ]);

    $options = [
        "http" => [
            "method" => "POST",
            "header" =>
                "Content-type: application/x-www-form-urlencoded\r\n" .
                "Accept: application/sparql-results+json\r\n",
            "content" => $data
        ]
    ];

    $context = stream_context_create($options);

    $response = file_get_contents(
        $url,
        false,
        $context
    );

    $result = json_decode($response, true);

    $item = $result['results']['bindings'][0] ?? [];

    $type = $item['type']['value'] ?? '';

    $genre = $item['genre']['value'] ?? '-';
    $tahun = $item['tahun']['value'] ?? '-';
    $rating = $item['rating']['value'] ?? '-';
    $sutradara = $item['sutradara']['value'] ?? '-';
    $penyanyi = $item['penyanyi']['value'] ?? '-';

    // hapus URL RDF
    $genre = str_replace(
        "http://example.org/film-musik#",
        "",
        $genre
    );

    $sutradara = str_replace(
        "http://example.org/film-musik#",
        "",
        $sutradara
    );

    $penyanyi = str_replace(
        "http://example.org/film-musik#",
        "",
        $penyanyi
    );

    // cek tipe data
    $isFilm = str_contains($type, "Film");

    $icon = $isFilm ? "🎬" : "🎵";

    echo "

    <div class='card'>

        <div class='icon'>
            $icon
        </div>

        <h1>$nama</h1>

        <div class='detail'>
            <span class='label'>Genre:</span>
            $genre
        </div>

        <div class='detail'>
            <span class='label'>Tahun Rilis:</span>
            $tahun
        </div>

        <div class='detail'>
            <span class='label'>Rating:</span>
            $rating
        </div>

    ";

    // DETAIL FILM
    if($isFilm){

        echo "

        <div class='detail'>
            <span class='label'>Sutradara:</span>
            $sutradara
        </div>

        ";

    }

    // DETAIL MUSIK
    else{

        echo "

        <div class='detail'>
            <span class='label'>Artist/Penyanyi:</span>
            $penyanyi
        </div>

        ";

    }

    echo "

        <a href='index.php'>
            ← Kembali
        </a>

    </div>

    ";
}

?>

</div>

</body>
</html>