<?php
ob_start();
session_start();
require_once "../config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}

require("fpdf/fpdf.php");

$users = $conn->query("SELECT id, pseudo, nom, prenom, email, statut, role, date_inscription FROM users ORDER BY id ASC");
$users = $users ? $users->fetch_all(MYSQLI_ASSOC) : [];

$pdf = new FPDF();
$pdf->AddPage('L');

$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Liste des utilisateurs - Audify', 0, 1, 'C');

$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 10, 'ID', 1);
$pdf->Cell(35, 10, 'Pseudo', 1);
$pdf->Cell(40, 10, 'Nom', 1);
$pdf->Cell(40, 10, 'Prenom', 1);
$pdf->Cell(60, 10, 'Email', 1);
$pdf->Cell(30, 10, 'Statut', 1);
$pdf->Cell(20, 10, 'Role', 1);
$pdf->Cell(35, 10, 'Inscription', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 9);
foreach ($users as $user) {
    $pdf->Cell(10, 10, $user['id'], 1);
    $pdf->Cell(35, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $user['pseudo']), 1);
    $pdf->Cell(40, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $user['nom']), 1);
    $pdf->Cell(40, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $user['prenom']), 1);
    $pdf->Cell(60, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $user['email']), 1);
    $pdf->Cell(30, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $user['statut']), 1);
    $pdf->Cell(20, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $user['role']), 1);
    $pdf->Cell(35, 10, isset($user['date_inscription']) ? date('d/m/Y', strtotime($user['date_inscription'])) : '-', 1);
    $pdf->Ln();
}

ob_end_clean();
$pdf->Output('D', 'utilisateurs_audify.pdf');
exit();
?>