<?php
session_start();

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

// Include the database connection
require_once '../config/database.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Get database connection
$conn = Database::getConnection();

// Fetch completed bookings
$sql = "SELECT 
            CONCAT(u.fname, ' ', u.lname) as customer_name,
            b.service,
            b.location,
            b.phone,
            b.appointment_date,
            b.appointment_time,
            b.note,
            b.status,
            COALESCE(e.name, 'Unassigned') as technician_name,
            b.created_at
        FROM bookings b
        LEFT JOIN user u ON b.user_id = u.id
        LEFT JOIN employees e ON b.employee_id = e.id
        WHERE b.status = 'Completed'
        ORDER BY b.appointment_date DESC, b.appointment_time DESC";

$result = $conn->query($sql);

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set document properties
$spreadsheet->getProperties()
    ->setCreator('AirGo Admin')
    ->setLastModifiedBy('AirGo Admin')
    ->setTitle('Completed Bookings Report')
    ->setSubject('Completed Bookings Export')
    ->setDescription('Export of completed bookings from AirGo system');

// Set column headers
$headers = [
    'A1' => 'Customer Name',
    'B1' => 'Service',
    'C1' => 'Location',
    'D1' => 'Contact',
    'E1' => 'Date',
    'F1' => 'Time',
    'G1' => 'Notes',
    'H1' => 'Status',
    'I1' => 'Technician',
    'J1' => 'Created At'
];

// Style the header row
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF']
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'color' => ['rgb' => '07353F']
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
        ]
    ]
];

// Apply headers and styling
foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}
$sheet->getStyle('A1:J1')->applyFromArray($headerStyle);

// Add data rows
$row = 2;
while ($booking = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $row, $booking['customer_name']);
    $sheet->setCellValue('B' . $row, $booking['service']);
    $sheet->setCellValue('C' . $row, $booking['location']);
    $sheet->setCellValue('D' . $row, $booking['phone']);
    $sheet->setCellValue('E' . $row, date('M d, Y', strtotime($booking['appointment_date'])));
    $sheet->setCellValue('F' . $row, date('h:i A', strtotime($booking['appointment_time'])));
    $sheet->setCellValue('G' . $row, $booking['note']);
    $sheet->setCellValue('H' . $row, $booking['status']);
    $sheet->setCellValue('I' . $row, $booking['technician_name']);
    $sheet->setCellValue('J' . $row, date('M d, Y h:i A', strtotime($booking['created_at'])));
    $row++;
}

// Style the data rows
$lastRow = $row - 1;
$dataRange = 'A2:J' . $lastRow;
$dataStyle = [
    'alignment' => [
        'vertical' => Alignment::VERTICAL_CENTER
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => 'CCCCCC']
        ]
    ]
];
$sheet->getStyle($dataRange)->applyFromArray($dataStyle);

// Auto-size columns
foreach (range('A', 'J') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Add alternating row colors
for ($i = 2; $i <= $lastRow; $i++) {
    if ($i % 2 == 0) {
        $sheet->getStyle('A' . $i . ':J' . $i)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F8F9FA');
    }
}

// Set title above the table
$sheet->insertNewRowBefore(1);
$sheet->mergeCells('A1:J1');
$sheet->setCellValue('A1', 'AirGo - Completed Bookings Report');
$sheet->getStyle('A1')->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 16,
        'color' => ['rgb' => '07353F']
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ]
]);
$sheet->getRowDimension(1)->setRowHeight(30);

// Add export date
$sheet->insertNewRowBefore(2);
$sheet->mergeCells('A2:J2');
$sheet->setCellValue('A2', 'Generated on: ' . date('F d, Y h:i A'));
$sheet->getStyle('A2')->applyFromArray([
    'font' => [
        'italic' => true,
        'size' => 10,
        'color' => ['rgb' => '666666']
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ]
]);

// Set the worksheet name
$sheet->setTitle('Completed Bookings');

// Create the Excel file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="AirGo_Completed_Bookings_' . date('Y-m-d') . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit; 