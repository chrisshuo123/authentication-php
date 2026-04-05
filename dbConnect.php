<?php 
$host="localhost:3307";
// $host="localhost";
$dbname='auth';
$username='root';
$password='';

$pdo=new PDO("mysql:host=$host;dbname=$dbname",$username,$password);
