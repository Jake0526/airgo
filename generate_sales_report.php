<?php
require 'vendor/autoload.php';
require_once 'config/database.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

// Get date range from request
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Create new spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set column widths
$sheet->getColumnDimension('A')->setWidth(15);  // Date
$sheet->getColumnDimension('B')->setWidth(25);  // Service Type
$sheet->getColumnDimension('C')->setWidth(15);  // Price
$sheet->getColumnDimension('D')->setWidth(25);  // Customer Name
$sheet->getColumnDimension('E')->setWidth(20);  // Contact
$sheet->getColumnDimension('F')->setWidth(40);  // Location
$sheet->getColumnDimension('G')->setWidth(30);  // Proof of Payment

// Add company logo if exists
if (file_exists('assets/images/logo.png')) {
    $drawing = new Drawing();
    $drawing->setName('Logo');
    $drawing->setDescription('Company Logo');
    $drawing->setPath('assets/images/logo.png');
    $drawing->setCoordinates('A1');
    $drawing->setWidth(100);
    $drawing->setHeight(100);
    $drawing->setWorksheet($sheet);
}

// Add report header
$sheet->mergeCells('C1:G1');
$sheet->setCellValue('C1', 'AIRGO AIRCONDITIONING SERVICES');
$sheet->mergeCells('C2:G2');
$sheet->setCellValue('C2', 'Sales Report');
$sheet->mergeCells('C3:G3');
$sheet->setCellValue('C3', 'Period: ' . date('F d, Y', strtotime($start_date)) . ' to ' . date('F d, Y', strtotime($end_date)));

// Style header
$headerStyle = [
    'font' => [
        'bold' => true,
        'size' => 16,
        'color' => ['rgb' => '07353F'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
];
$sheet->getStyle('C1')->applyFromArray($headerStyle);
$sheet->getStyle('C2')->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 14,
        'color' => ['rgb' => '07353F'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
    ],
]);
$sheet->getStyle('C3')->applyFromArray([
    'font' => [
        'size' => 12,
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
    ],
]);

// Start table headers from row 5
$tableHeaderRow = 5;
$sheet->setCellValue('A' . $tableHeaderRow, 'Date');
$sheet->setCellValue('B' . $tableHeaderRow, 'Service Type');
$sheet->setCellValue('C' . $tableHeaderRow, 'Price');
$sheet->setCellValue('D' . $tableHeaderRow, 'Customer Name');
$sheet->setCellValue('E' . $tableHeaderRow, 'Contact');
$sheet->setCellValue('F' . $tableHeaderRow, 'Location');
$sheet->setCellValue('G' . $tableHeaderRow, 'Proof of Payment');

// Style table headers
$tableHeaderStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '07353F'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000'],
        ],
    ],
];

$sheet->getStyle('A' . $tableHeaderRow . ':G' . $tableHeaderRow)->applyFromArray($tableHeaderStyle);

// Initialize database connection
$conn = Database::getConnection();

// Fetch only completed bookings from database
$query = "SELECT b.*, u.fname, u.lname, u.contact, b.payment_proof
          FROM bookings b 
          LEFT JOIN user u ON b.user_id = u.id 
          WHERE b.appointment_date BETWEEN ? AND ?
          AND b.status = 'Completed'
          ORDER BY b.appointment_date ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

$row = $tableHeaderRow + 1;
$totalRevenue = 0;

while ($booking = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $row, date('Y-m-d', strtotime($booking['appointment_date'])));
    $sheet->setCellValue('B' . $row, $booking['service']);
    $sheet->setCellValue('C' . $row, $booking['price']);
    $sheet->setCellValue('D' . $row, $booking['fname'] . ' ' . $booking['lname']);
    $sheet->setCellValue('E' . $row, $booking['contact']);
    $sheet->setCellValue('F' . $row, $booking['location']);

    // Add proof of payment image if available
    if (!empty($booking['payment_proof'])) {
        $imagePath = $booking['payment_proof'];  // The path is already stored with the prefix in the database
        if (file_exists($imagePath)) {
            $drawing = new Drawing();
            $drawing->setName('Payment Proof');
            $drawing->setDescription('Payment Proof');
            $drawing->setPath($imagePath);
            $drawing->setCoordinates('G' . $row);
            $drawing->setWidth(100);
            $drawing->setHeight(80);
            $drawing->setWorksheet($sheet);
            $sheet->getRowDimension($row)->setRowHeight(60);
        } else {
            $sheet->setCellValue('G' . $row, 'Image not found: ' . $imagePath);
        }
    } else {
        $sheet->setCellValue('G' . $row, 'No proof uploaded');
    }

    // Add to total revenue
    $totalRevenue += $booking['price'];

    // Style the data rows
    $rowStyle = [
        'alignment' => [
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ];

    $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray($rowStyle);
    $row++;
}

// Add total revenue row
$totalRow = $row;
$sheet->mergeCells('A' . $totalRow . ':B' . $totalRow);
$sheet->setCellValue('A' . $totalRow, 'Total Revenue');
$sheet->setCellValue('C' . $totalRow, $totalRevenue);

$totalStyle = [
    'font' => [
        'bold' => true,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'E8F6FF'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_RIGHT,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000'],
        ],
    ],
];

$sheet->getStyle('A' . $totalRow . ':C' . $totalRow)->applyFromArray($totalStyle);

// Format the price column
$sheet->getStyle('C' . $tableHeaderRow . ':C' . $totalRow)
    ->getNumberFormat()
    ->setFormatCode('₱#,##0.00');

// Set print area
$sheet->getPageSetup()->setPrintArea('A1:G' . $totalRow);
$sheet->getPageSetup()->setFitToWidth(1);
$sheet->getPageSetup()->setFitToHeight(0);

// Set the content type and headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="sales_report_' . $start_date . '_to_' . $end_date . '.xlsx"');
header('Cache-Control: max-age=0');

// Save the spreadsheet to PHP output
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
?> 