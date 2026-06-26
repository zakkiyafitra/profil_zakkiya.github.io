<!DOCTYPE html>
<html>
<head>
    <title>Sistem Rekomendasi Film & Musik</title>

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Arial;
        }

        body{
            background:#0f172a;
            color:white;
        }

        .container{
            width:90%;
            margin:auto;
            padding:30px 0;
        }

        /* NAVBAR */

        .navbar{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:50px;
        }

        .logo{
            font-size:28px;
            font-weight:bold;
        }

        .menu a{
            color:white;
            text-decoration:none;
            margin-left:25px;
            transition:0.3s;
            font-size:16px;
        }

        .menu a:hover{
            color:#60a5fa;
        }

        /* HERO */

        .hero{
            text-align:center;
            margin-bottom:50px;
        }

        .hero h1{
            font-size:50px;
            margin-bottom:20px;
        }

        .hero p{
            color:#cbd5e1;
            font-size:18px;
            margin-bottom:30px;
        }

        /* BUTTON */

        button{
            padding:14px 28px;
            background:#2563eb;
            color:white;
            border:none;
            border-radius:10px;
            font-size:16px;
            cursor:pointer;
            transition:0.3s;
        }

        button:hover{
            background:#1d4ed8;
            transform:scale(1.05);
        }

        /* SEARCH */

        .search-box{
            text-align:center;
            margin-bottom:50px;
        }

        input{
            width:320px;
            padding:14px;
            border:none;
            border-radius:10px;
            outline:none;
            margin-right:10px;
            font-size:15px;
        }

        /* STATS */

        .stats{
            display:flex;
            justify-content:center;
            gap:20px;
            margin-bottom:50px;
            flex-wrap:wrap;
        }

        .stat-box{
            background:#1e293b;
            padding:25px 40px;
            border-radius:15px;
            text-align:center;
            min-width:180px;
            box-shadow:0 4px 10px rgba(74, 227, 238, 0.93);
        }

        .stat-box h2{
            font-size:35px;
            margin-bottom:10px;
            color:#60a5fa;
        }

        /* CARD */

        .card-container{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
            gap:25px;
        }

        .card{
            background:#1e293b;
            border-radius:18px;
            overflow:hidden;
            transition:0.3s;
            box-shadow:0 4px 12px rgb(255, 255, 255);
        }

        .card:hover{
            transform:translateY(-8px);
        }

        .poster{
            height:280px;
            background:#334155;
            display:flex;
            justify-content:center;
            align-items:center;
            font-size:70px;
        }

        .content{
            padding:20px;
        }

        .content h3{
            margin-bottom:15px;
            font-size:24px;
        }

        .genre{
            display:inline-block;
            padding:7px 14px;
            background:#2563eb;
            border-radius:20px;
            font-size:13px;
            margin-bottom:12px;
        }

        .detail-btn{
            display:inline-block;
            margin-top:15px;
            padding:10px 18px;
            background:#0f172a;
            color:white;
            text-decoration:none;
            border-radius:10px;
            transition:0.3s;
        }

        .detail-btn:hover{
            background:#2563eb;
        }

        .empty{
            text-align:center;
            margin-top:40px;
            color:#cbd5e1;
            font-size:20px;
        }

        footer{
            text-align:center;
            margin-top:70px;
            padding:30px;
            color:#94a3b8;
            border-top:1px solid #56ccff;
        }

    </style>

</head>
<body>

<div class="container">

    <!-- NAVBAR -->

    <div class="navbar">

        <div class="logo">
             
        </div>

        <div class="menu">
            <a href="index.php">Beranda</a>
            <a href="?tampil=1">Daftar Data</a>
            <a href="#search">Pencarian</a>
            <a href="index.html">Profil</a>
        </div>

    </div>

    <!-- HERO -->

    <div class="hero">

        <h1>
             Sistem Rekomendasi Film & Musik
        </h1>

        <form method="GET">

            <button type="submit" name="tampil">
                Tampilkan Film & Musik
            </button>

        </form>

    </div>

    <!-- STATISTIK -->

    <div class="stats">

        <div class="stat-box">
            <h2>10</h2>
            <p>Total Film</p>
        </div>

        <div class="stat-box">
            <h2>10</h2>
            <p>Total Musik</p>
        </div>

        <div class="stat-box">
            <h2>10</h2>
            <p>Total Genre</p>
        </div>

    </div>

    <!-- SEARCH -->

    <div class="search-box" id="search">

        <form method="GET">

            <input
                type="text"
                name="keyword"
                placeholder="Cari film..."
                required
            >

            <button type="submit" name="cari">
                Cari
            </button>

        </form>

    </div>

<?php

$url = "http://localhost:3030/filmdb/query";

/* ==========================
   FITUR PENCARIAN
========================== */

if(isset($_GET['cari'])){

    $keyword = $_GET['keyword'];

    $query = "

PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX ex: <http://example.org/film-musik#>

SELECT ?item ?type
WHERE {

    {
        ?item rdf:type ex:Film .
        BIND('Film' AS ?type)
    }

    UNION

    {
        ?item rdf:type ex:Musik .
        BIND('Musik' AS ?type)
    }

    FILTER(
        CONTAINS(
            LCASE(
                REPLACE(
                    STR(?item),
                    'http://example.org/film-musik#',
                    ''
                )
            ),
            LCASE('$keyword')
        )
    )

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

    if(empty($result['results']['bindings'])){

        echo "
        <div class='empty'>
            Data tidak ditemukan
        </div>
        ";

    }else{

        echo "<div class='card-container'>";

        foreach($result['results']['bindings'] as $item){

            $nama = $item['item']['value'];

            $type = $item['type']['value'];

            $nama = str_replace(
                "http://example.org/film-musik#",
                "",
                $nama
            );

            $isFilm = str_contains($type, "Film");

            $icon = $isFilm ? "🎬" : "🎵";

            $label = $isFilm ? "Film" : "Musik";

            echo "

            <div class='card'>

                <div class='poster'>
                    $icon
                </div>

                <div class='content'>

                    <h3>$nama</h3>

                    <span class='genre'>
                        $label
                    </span>

                    <br><br>

                    <a href='detail.php?film=".urlencode($nama)."'
                       class='detail-btn'>

                        Detail

                    </a>

                </div>

            </div>

            ";
        }

        echo "</div>";
    }
}

function tampilkanCard($result){

    echo "<div class='card-container'>";

    foreach($result['results']['bindings'] as $item){

        $film = $item['film']['value'];

        $film = str_replace(
            "http://example.org/film-musik#",
            "",
            $film
        );

        echo "

        <div class='card'>

            <div class='poster'>
                🎥
            </div>

            <div class='content'>

                <h3>$film</h3>

                <span class='genre'>
                    Movie
                </span>

                <br>

                <a href='detail.php?film=".urlencode($film)."'
                   class='detail-btn'>

                   Detail

                </a>

            </div>

        </div>

        ";
    }

    echo "</div>";
}
/* ==========================
   TAMPILKAN SEMUA FILM
========================== */

if(isset($_GET['tampil'])){

    $query = "

    PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
    PREFIX ex: <http://example.org/film-musik#>

    SELECT ?item ?type
    WHERE {

        {
            ?item rdf:type ex:Film .
            BIND('Film' AS ?type)
        }

        UNION

        {
            ?item rdf:type ex:Musik .
            BIND('Musik' AS ?type)
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

    if(empty($result['results']['bindings'])){

        echo "
        <div class='empty'>
            Data tidak ditemukan
        </div>
        ";

    }else{

        echo "<div class='card-container'>";

        foreach($result['results']['bindings'] as $item){

            $dataItem = $item['item']['value'];

            $type = $item['type']['value'];

            $dataItem = str_replace(
                "http://example.org/film-musik#",
                "",
                $dataItem
            );

            $icon = ($type == "Film") ? "🎥" : "🎵";

            echo "

            <div class='card'>

                <div class='poster'>
                    $icon
                </div>

                <div class='content'>

                    <h3>$dataItem</h3>

                    <span class='genre'>
                        $type
                    </span>

                    <br>

                    <a href='detail.php?film=".urlencode($dataItem)."'
                       class='detail-btn'>

                       Detail

                    </a>

                </div>

            </div>

            ";
        }

        echo "</div>";

    }
}
