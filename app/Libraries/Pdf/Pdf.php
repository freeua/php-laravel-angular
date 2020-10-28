<?php

namespace App\Libraries\Pdf;

use Config;
use Mpdf;

/**
 * Laravel PDF: mPDF wrapper for Laravel 5
 *
 * @package laravel-pdf
 * @author  Niklas Ravnsborg-Gjertsen
 */
class Pdf
{
    /** @var Mpdf\Mpdf */
    public $mpdf;
    /** @var array */
    protected $config = [];

    /**
     * Pdf constructor.
     *
     * @param string $html
     * @param array  $config
     *
     * @throws Mpdf\MpdfException
     */
    public function __construct($html = '', $config = [])
    {
        $this->config = $config;
        $mpdfConfig = [
            'mode'              => $this->getConfig('mode'),
            'format'            => $this->getConfig('format'),
            'default_font_size' => $this->getConfig('default_font_size'),
            'default_font'      => $this->getConfig('default_font'),
            'margin_left'       => $this->getConfig('margin_left'),
            'margin_right'      => $this->getConfig('margin_right'),
            'margin_top'        => $this->getConfig('margin_top'),
            'margin_bottom'     => $this->getConfig('margin_bottom'),
            'margin_header'     => $this->getConfig('margin_header'),
            'margin_footer'     => $this->getConfig('margin_footer'),
            'orientation'       => $this->getConfig('orientation'),
            'tempDir'       => $this->getConfig('tempDir'),
        ];

        // Handle custom fonts
        $mpdfConfig = $this->addCustomFontsConfig($mpdfConfig);

        $this->mpdf = new Mpdf\Mpdf($mpdfConfig);

        // If you want to change your document title,
        // please use the <title> tag.
        $this->mpdf->SetTitle('Document');
        $this->mpdf->SetAuthor($this->getConfig('author'));
        $this->mpdf->SetCreator($this->getConfig('creator'));
        $this->mpdf->SetSubject($this->getConfig('subject'));
        $this->mpdf->SetKeywords($this->getConfig('keywords'));
        $this->mpdf->SetDisplayMode($this->getConfig('display_mode'));
        $this->mpdf->WriteHTML($html);
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    protected function getConfig($key)
    {
        return isset($this->config[$key])
            ? $this->config[$key]
            : Config::get('pdf.' . $key);
    }

    /**
     * @param $mpdfConfig
     *
     * @return mixed
     */
    protected function addCustomFontsConfig($mpdfConfig)
    {
        if (!Config::has('pdf.font_path') || !Config::has('pdf.font_data')) {
            return $mpdfConfig;
        }

        // Get default font configuration
        $fontDirs = (new Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'];
        $fontData = (new Mpdf\Config\FontVariables())->getDefaults()['fontdata'];
        // Merge default with custom configuration
        $mpdfConfig['fontDir'] = array_merge($fontDirs, [Config::get('pdf.font_path')]);
        $mpdfConfig['fontdata'] = array_merge($fontData, Config::get('pdf.font_data'));

        return $mpdfConfig;
    }

    /**
     * Encrypts and sets the PDF document permissions
     *
     * @param array  $permission    Permissions e.g.: ['copy', 'print']
     * @param string $userPassword  User password
     * @param string $ownerPassword Owner password
     *
     * @return static
     *
     */
    public function setProtection($permission, $userPassword = '', $ownerPassword = '')
    {
        if (func_get_args()[2] === null) {
            $ownerPassword = bin2hex(openssl_random_pseudo_bytes(8));
        };

        return $this->mpdf->SetProtection($permission, $userPassword, $ownerPassword);
    }

    /**
     * Output the PDF as a string.
     *
     * @return string The rendered PDF as string
     * @throws Mpdf\MpdfException
     */
    public function output()
    {
        return $this->mpdf->Output('', 'S');
    }

    /**
     * Save the PDF to a file
     *
     * @param $filename
     *
     * @return static
     * @throws Mpdf\MpdfException
     */
    public function save($filename)
    {
        return $this->mpdf->Output($filename, 'F');
    }

    /**
     * Make the PDF downloadable by the user
     *
     * @param string $filename
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws Mpdf\MpdfException
     */
    public function download($filename = 'document.pdf')
    {
        return $this->mpdf->Output($filename, 'D');
    }

    /**
     * Return a response with the PDF to show in the browser
     *
     * @param string $filename
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws Mpdf\MpdfException
     */
    public function stream($filename = 'document.pdf')
    {
        return $this->mpdf->Output($filename, 'I');
    }


    public function WriteHTML($html)
    {
        return $this->mpdf->WriteHTML($html);
    }
}
