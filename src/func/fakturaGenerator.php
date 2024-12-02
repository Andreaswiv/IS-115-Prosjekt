<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../src/resources/inc/setupdb/setup.php';

use TCPDF;

function generateInvoiceWithTCPDF($userId, $bookingId, $conn) {
    // Opprett en ny TCPDF-instans
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);

    // Hent bookingdata og brukerdata fra databasen
    $query = "
        SELECT 
            u.id as user_id, u.firstName, u.lastName, u.address, u.postalCode, u.email,
            b.room_id, b.room_type, b.start_date, b.end_date,
            r.room_name
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN rooms r ON b.room_id = r.id
        WHERE b.id = :bookingId AND u.id = :userId
    ";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':bookingId', $bookingId, PDO::PARAM_INT);
    $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        throw new Exception("Ingen data funnet for bruker $userId og booking $bookingId.");
    }

    // Beregn priser
    $roomPrices = ['single' => 1000, 'double' => 1500, 'king suite' => 2500];
    $roomType = strtolower($data['room_type']); // Standardiser
    if (!array_key_exists($roomType, $roomPrices)) {
        throw new Exception("Ukjent romtype: $roomType");
    }

    $days = (strtotime($data['end_date']) - strtotime($data['start_date'])) / (60 * 60 * 24);
    $netPrice = $roomPrices[$roomType] * $days;
    $vat = $netPrice * 0.25;
    $totalPrice = $netPrice + $vat;

    // Skriv data til PDF
    $pdf->Cell(0, 10, "Faktura for booking $bookingId", 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->Cell(0, 10, "Kundenavn: " . $data['firstName'] . " " . $data['lastName'], 0, 1);
    $pdf->Cell(0, 10, "Adresse: " . $data['address'], 0, 1);
    $pdf->Cell(0, 10, "Postnummer: " . $data['postalCode'], 0, 1);
    $pdf->Ln(5);
    $pdf->Cell(0, 10, "Romtype: " . ucfirst($data['room_type']), 0, 1);
    $pdf->Cell(0, 10, "Fra: " . $data['start_date'] . " Til: " . $data['end_date'], 0, 1);
    $pdf->Ln(5);
    $pdf->Cell(0, 10, "Netto pris: " . number_format($netPrice, 2) . " NOK", 0, 1);
    $pdf->Cell(0, 10, "MVA (25%): " . number_format($vat, 2) . " NOK", 0, 1);
    $pdf->Cell(0, 10, "Totalpris: " . number_format($totalPrice, 2) . " NOK", 0, 1);

    // Send PDF til nettleseren som en nedlasting
    $pdf->Output("Faktura_$bookingId.pdf", 'D'); // 'D' for nedlasting
}

if (isset($_GET['booking_id'])) {
    session_start();
    if (!isset($_SESSION['user_id'])) {
        die("Du må være logget inn for å laste ned faktura.");
    }

    $userId = $_SESSION['user_id'];
    $bookingId = $_GET['booking_id'];

    try {
        $database = new Database();
        $conn = $database->getConnection();
        generateInvoiceWithTCPDF($userId, $bookingId, $conn);
    } catch (Exception $e) {
        echo "En feil oppstod: " . $e->getMessage();
    }
}
?>
