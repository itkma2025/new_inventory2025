<?php
include "akses.php";
include "koneksi.php";
require_once('tcpdf/tcpdf.php');

// Get the ID of the invoice from the query string
$id_inv_bum = base64_decode($_GET['id']);

// Get the cs folder name from the database based on the invoice ID for inv_bum
$query_cs_bum = mysqli_query($connect, 
                             "SELECT id_inv, id_inv_bum, nama_cs, nama_invoice
                              FROM spk_reg
                              LEFT JOIN inv_bum ON (spk_reg.id_inv = inv_bum.id_inv_bum)
                              LEFT JOIN tb_customer ON (spk_reg.id_customer = tb_customer.id_cs)
                              WHERE spk_reg.id_inv = '$id_inv_bum';
                            ");

$row_cs_bum = mysqli_fetch_array($query_cs_bum);
$cs_bum = $row_cs_bum['nama_cs'];
$nama_invoice = $row_cs_bum['nama_invoice'];

$no_inv_bum = $row_cs_bum['id_inv_bum'];

// Convert $no_inv_bum to the desired format
$no_inv_bum_converted = str_replace('/', '_', $no_inv_bum);

// Generate folder name based on invoice details
$folder_name = $no_inv_bum_converted;

// Encode a portion of the folder name
$encoded_portion = base64_encode($folder_name);

// Combine the original $no_inv_bum, encoded portion, and underscore
$encoded_folder_name = $no_inv_bum_converted . '_' . $encoded_portion;

// Set the path for the customer's folder
$customer_folder_path = "../Customer/" . $cs_bum . "/" . date('Y') . "/" . date('m') . "/" . date('d') . "/Invoice Bum/" . $encoded_folder_name;

// Create the customer's folder if it doesn't exist
if (!is_dir($customer_folder_path)) {
    mkdir($customer_folder_path, 0777, true); // Set permission to 0777 to ensure the folder is writable
}

// Generate the PDF content
$pdf_content = '<html>
<head>
<style>
body {
    font-family: Arial, sans-serif;
}
</style>
</head>
<body>
<h1>Invoice</h1>
<p>This is the content of the invoice.</p>
</body>
</html>';

// Generate a unique filename for the PDF
$filename = 'invoice_' . $no_inv_bum . '.pdf';

// Set the path for the PDF file
// $pdf_file_path = $customer_folder_path . '/' . $filename;
$pdf_file_path = __DIR__ . '/invoice.pdf';


// Create a new TCPDF instance
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set the document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Invoice');
$pdf->SetSubject('Invoice PDF');
$pdf->SetKeywords('Invoice, PDF');

// Set default header and footer data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Set some language-dependent strings
$pdf->setLanguageArray(array(
    'a_meta_charset' => 'UTF-8',
    'a_meta_dir' => 'ltr',
    'a_meta_language' => 'en',
    'w_page' => 'page'
));

// Add a page
$pdf->AddPage();

// Write the PDF content
$pdf->writeHTML($pdf_content, true, false, true, false, '');

// Output the PDF file
$pdf->Output($pdf_file_path, 'F');

// Redirect the user to download the generated PDF
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
readfile($pdf_file_path);
?>
