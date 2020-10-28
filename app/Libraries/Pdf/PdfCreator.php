<?php

namespace App\Libraries\Pdf;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Mpdf\Mpdf;

/**
 * Class PdfCreator
 *
 * @package App\Libraries\Pdf
 */
class PdfCreator
{
    /**
     * Make a pdf instance
     *
     * @param array $config
     *
     * @return Pdf
     * @throws \Mpdf\MpdfException
     */
    public function make(array $config = [])
    {
        return new Pdf('', $config);
    }

    /**
     * Load a HTML string
     *
     * @param string $html
     * @param array $config
     *
     * @return Pdf
     * @throws \Mpdf\MpdfException
     */
    public function loadHTML(string $html, array $config = [])
    {
        return new Pdf($html, $config);
    }

    /**
     * Load a HTML file
     *
     * @param string $file
     * @param array $config
     *
     * @return Pdf
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Mpdf\MpdfException
     */
    public function loadFile(string $file, array $config = []): Pdf
    {
        return new Pdf(File::get($file), $config);
    }

    /**
     * Load a View and convert to HTML
     *
     * @param string $view
     * @param array $data
     * @param array $mergeData
     * @param array $config
     *
     * @return Pdf
     * @throws \Mpdf\MpdfException
     */
    public function loadView(string $view, array $data = [], array $mergeData = [], array $config = []): Pdf
    {
        $pdf = self::make();
        $pdf->mpdf->setBasePath(public_path());
        $rendered = View::make($view, $data, $mergeData)->render();
        self::loadHtmlInChunks($pdf, $rendered);
        return $pdf;
    }

    public function loadHtmlInChunks($mpdf, string $html)
    {
        $long_html = strlen($html);
        $long_int  = intval($long_html/100000);

        if ($long_int > 0) {
            for ($i = 0; $i<$long_int; $i++) {
                $temp_html = substr($html, ($i*100000), 99999);
                $mpdf->WriteHTML($temp_html);
            }
            //Last block
            $temp_html = substr($html, ($i*100000), ($long_html-($i*100000)));
            $mpdf->WriteHTML($temp_html);
        } else {
            $mpdf->WriteHTML($html);
        }
    }
}
