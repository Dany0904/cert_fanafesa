<?php

require('../../config.php');
require_login();

global $CFG;

require_once($CFG->libdir . '/pdflib.php');

$userid = required_param('userid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);

/*
 * 1. Obtener datos estructurados
 */
$data = \local_cert_fanafesa\certificate_data::get($userid, $courseid);

/*
 * 2. Render HTML desde Mustache
 */
$html = \local_cert_fanafesa\pdf\renderer::render($data);

/*
 * (OPCIONAL DEBUG)
 */
// echo $html;
// die();

/*
 * 3. Generar PDF
 */
$pdf = new \pdf();

$pdf->SetTitle('Certificado FANAFESA');
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

/*
 * Importante para HTML complejo
 */
$pdf->SetFont('helvetica', '', 10);
$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Output('certificado.pdf', 'I');