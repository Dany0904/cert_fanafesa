<?php

namespace local_cert_fanafesa;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/pdflib.php');

class certificate_generator {

    public static function generate(
        int $userid,
        int $courseid
    ): string {

        /*
         * Datos
         */

        $data = certificate_data::get(
            $userid,
            $courseid
        );

        /*
         * HTML
         */

        $html =
            pdf\renderer::render(
                $data
            );

        /*
         * PDF
         */

        $pdf = new \pdf();

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->SetTitle(
            'Certificado FANAFESA'
        );

        $pdf->SetMargins(
            25,
            25,
            25
        );

        $pdf->AddPage();

        $pdf->SetFont(
            'helvetica',
            '',
            10
        );

        $pdf->writeHTML(
            $html,
            true,
            false,
            true,
            false,
            ''
        );

        $pdf->setPage(1);

        $pdf->SetLineWidth(0.3);

        $pdf->Line(27, 238, 75, 238);

        $pdf->Line(80, 238, 130, 238);

        $pdf->Line(135, 238, 182, 238);

        self::draw_boxes(
            $pdf,
            25.4,
            96,
            $data['curp'],
            18,
            4.4
        );

        self::draw_boxes(
            $pdf,
            25.4,
            138.2,
            $data['rfcempresa'],
            15,
            5.2,
            8.5,
            2,
            true
        );

        self::draw_boxes(
            $pdf,
            101.6,
            173,
            $data['inicioanio'],
            4,
            4.75,
            6
        );

        self::draw_boxes(
            $pdf,
            121,
            173,
            $data['iniciomes'],
            2,
            4.75,
            6
        );

        self::draw_boxes(
            $pdf,
            130.8,
            173,
            $data['iniciodia'],
            2,
            4.75,
            6
        );

        self::draw_boxes(
            $pdf,
            147,
            173,
            $data['finalanio'],
            4,
            4.75,
            6
        );

        self::draw_boxes(
            $pdf,
            166,
            173,
            $data['finalmes'],
            2,
            4.75,
            6
        );

        self::draw_boxes(
            $pdf,
            175.5,
            173,
            $data['finaldia'],
            2,
            4.75,
            6
        );

        $pdf->SetLineWidth(0.3);

        $pdf->Rect(
            25,
            203,
            160,
            40
        );

        $pdf->SetFont(
            'helvetica',
            '',
            7
        );

        $pdf->SetXY(
            170,
            267
        );

        $pdf->MultiCell(

            16,

            8,

            "DC-3\nANVERSO",

            0,

            'C',

            false

        );

        $pdf->setPage(2);

        $pdf->SetFont(
            'helvetica',
            '',
            7
        );

        $pdf->SetXY(
            170,
            267
        );

        $pdf->MultiCell(

            16,

            8,

            "DC-3\nREVERSO",

            0,

            'C',

            false

        );

        return $pdf->Output(
            '',
            'S'
        );

    }

    public static function download(

        int $userid,

        int $courseid

    ): void {

        $content = self::generate(

            $userid,

            $courseid

        );

        header(

            'Content-Type: application/pdf'

        );

        header(

            'Content-Disposition: inline; filename="certificado.pdf"'

        );

        echo $content;

        exit;

    }

    private static function draw_boxes(

        \pdf $pdf,

        float $x,

        float $y,

        string $text,

        int $totalboxes,

        float $boxwidth,

        float $boxheight = 8.5,

        int $offset = 0,

        bool $drawlastright = false

    ): void {

        $text = strtoupper(
            $text
        );

        $chars = array_merge(

            array_fill(
                0,
                $offset,
                ''
            ),

            str_split(
                $text
            )

        );

        $pdf->SetLineWidth(
            0.2
        );

        $pdf->SetFont(

            'helvetica',

            'B',

            8

        );

        for (

            $i = 0;

            $i < $totalboxes;

            $i++

        ) {

            $char =

                $chars[$i]

                ??

                '';

            $left =

                $x +

                ($i * $boxwidth);

            $right =

                $left +

                $boxwidth;

            if ($i > 0) {

                $pdf->Line(

                    $left,

                    $y,

                    $left,

                    $y + $boxheight

                );

            }

            if (

                $i < $totalboxes - 1 ||

                (

                    $drawlastright &&

                    $i === $totalboxes - 1

                )

            ) {

                $pdf->Line(

                    $right,

                    $y,

                    $right,

                    $y + $boxheight

                );

            }

            $pdf->Line(

                $left,

                $y + $boxheight,

                $right,

                $y + $boxheight

            );

            $pdf->SetXY(

                $left,

                $y + 0.3

            );

            $pdf->Cell(

                $boxwidth,

                $boxheight - 0.5,

                $char,

                0,

                0,

                'C'

            );

        }

    }

}