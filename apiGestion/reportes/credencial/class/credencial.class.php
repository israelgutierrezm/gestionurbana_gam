<?php
require('../../vendor/FPDF/fpdf.php');
require('../../vendor/codigo_qr/phpqrcode/qrlib.php');

class Credencial extends FPDF
{
    private $usuarioId;
    private $nombre;
    private $apPaterno;
    private $apMaterno;
    private $urlFoto;

    public function __construct($datosUsuario)
    {
        parent::__construct('P', 'mm', array(54, 86));
        $this->usuarioId = $datosUsuario['usuario_id'];
        $this->nombre = isset($datosUsuario['nombre']) ? $datosUsuario['nombre'] : NULL;
        $this->apPaterno = isset($datosUsuario['ap_pat']) ? $datosUsuario['ap_pat'] : NULL;
        $this->apMaterno = isset($datosUsuario['ap_mat']) ? $datosUsuario['ap_mat'] : NULL;
        $this->urlFoto = isset($datosUsuario['url_foto']) ? $datosUsuario['url_foto'] : NULL;
    }

    public function generaCredencial()
    {
        $this->SetAutoPageBreak(true, 5);
        $this->AliasNbPages();
        $this->AddPage();
        $this->frenteCredencial();
        $this->AddPage();
        $this->traseraCredencial();
        $nombreCompleto = mb_convert_encoding(mb_strtoupper($this->nombre . '_' . $this->apPaterno . '_' . $this->apMaterno), "ISO-8859-1", "UTF-8");
        $this->Output('I', 'credencial_' . $nombreCompleto . '.pdf');
    }

    private function frenteCredencial()
    {
        $urlFrenteCredencial = $GLOBALS['url_front'] . 'assets/credencial/frente.png';
        $this->Image($urlFrenteCredencial, 0, 0, 54, 86);
        if ($this->urlFoto) {
            $this->Image($GLOBALS['url_front'] . $this->urlFoto, 4, 13, 21, 27);
        }
        $urlRedirect = $GLOBALS['url_front_gam'].'admin/personal/info/'.$this->usuarioId;
        $this->generaCodigoQr($this->usuarioId, $urlRedirect);
        $this->Image('../../../assets/qr/' . $this->usuarioId . '.png', 15, 52, 23, 27);
        unlink('../../../assets/qr/' . $this->usuarioId . '.png');
        $this->SetXY(28,17);
        $this->SetFont('Arial', 'B', 8);
        $nombreCompleto = mb_convert_encoding(mb_strtoupper($this->nombre . ' ' . $this->apPaterno . ' ' . $this->apMaterno), "ISO-8859-1", "UTF-8");
        $this->MultiCell(23,4,$nombreCompleto, 0, 'C', false);
    }

    private function traseraCredencial()
    {
        $urlTraseraCredencial = $GLOBALS['url_front'] . 'assets/credencial/atras.jpeg';
        $this->Image($urlTraseraCredencial, 0, 0, 54, 86);
        $año_actual = date("y");
    }

    private function generaCodigoQr($usuarioId, $url){
        $dir = '../../../assets/qr';
        if (!file_exists($dir)) {
            mkdir($dir);
        }

        $dir_cache = '../../vendor/codigo_qr/phpqrcode/cache';
        if (!file_exists($dir_cache)) {
            mkdir($dir_cache);
        }

        $filename = $dir . '/' . $usuarioId . '.png';
        QRcode::png($url, $filename);
        if (file_exists('../../vendor/codigo_qr/phpqrcode/.png-errors.txt')) {
            unlink('../../vendor/codigo_qr/phpqrcode/.png-errors.txt');
        }
        if (file_exists('../../vendor/codigo_qr/phpqrcode/' . $usuarioId . '.png-errors.txt')) {
            unlink('../../vendor/codigo_qr/phpqrcode/' . $usuarioId . '.png-errors.txt');
        }
        $this->limpiarCacheQr();
    }

    private function limpiarCacheQr()
    {
        $carpeta = glob('../../vendor/codigo_qr/phpqrcode/cache/*');
        foreach ($carpeta as $archivo) {
            if (is_dir($archivo)) {
                $carpeta_sub = glob($archivo . '/*');
                foreach ($carpeta_sub as $archivo_sub) {
                    unlink($archivo_sub);
                }
                rmdir($archivo);
            } else {
                unlink($archivo);
            }
        }
    }
}
