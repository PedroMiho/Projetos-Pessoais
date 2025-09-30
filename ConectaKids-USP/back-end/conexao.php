<?php

    $hostname = 'localhost';
    $bancoDeDados = 'conectaKids';
    $usuario = 'root';
    $senha = '';

    $conn = new mysqli($hostname, $usuario, $senha, $bancoDeDados);
      if ($conn->connect_errno) {
        echo "Falha ao conectar: (". $conn->connect_errno. ")" ;
    }
?>