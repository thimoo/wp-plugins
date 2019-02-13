<?php

/**
 * Created by PhpStorm.
 * User: yanikpeiffer
 * Date: 03.08.16
 * Time: 15:53
 */

require_once 'vendor/autoload.php';

define('FPDF_FONTPATH', COMPASSION_LETTERS_PLUGIN_DIR_PATH . 'assets/fonts/');

class PDFGenerator
{

    /**
     * Generate the pdf file
     *
     * @param $data
     * @return string file-path
     */
    public static function generate($data, $directory) {
        $filename = time();
        $extension = '.pdf';

        $pdf = new FPDF();

        $pdf->AddPage();

        $pdf->Image(COMPASSION_LETTERS_PLUGIN_DIR_PATH . '/assets/images/' . $data['template'] . '.jpg', 0, 0, 210, 297);

        $pdf->SetFont('Arial','',10);

        /*
         * Add box with user info
         */
		$pdf->SetFillColor(255, 255, 255);
        $pdf->rect(100, 0, 75, 30, 'F');
        $user_info = "\nRef. enfant:\t" . $data['patenkind'] . "\nRef. parrain:\t" . $data['referenznummer'] ;
        $pdf->SetXY(100, 0);
        $pdf->MultiCell(73, 4, utf8_decode($user_info),0,'R',false);

        /*
         * Coordinates transparent box
         * X: 7
         * Y: 77
         */
		
		
		 /*add message*/
		  if(!empty($data['image'])) {
          
            $pdf->Image(COMPASSION_LETTERS_PLUGIN_DIR_PATH . '/assets/images/' . $data['template'] . '.jpg', 0, 0, 210, 297);

           $pdf->rect(100, 0, 75, 36, 'F');
             $pdf->SetXY(100, 0);
            $pdf->MultiCell(73, 6, utf8_decode($user_info),0,'R',false);
			$pdf->SetXY(12, 55);
	          $pdf->MultiCell(88, 5, utf8_decode(str_replace("\\", "", $data['message'])));


			$pdf->AddPage();
			$pdf->Image(COMPASSION_LETTERS_PLUGIN_DIR_PATH . '/assets/images/' . $data['template'] . '.jpg', 0, 0, 210, 297);
			$pdf->SetXY(100, 0);
            $pdf->MultiCell(73, 6, utf8_decode($user_info),0,'R',false);
            $pdf->SetXY(12, 55);
            $pdf->Image($data['image'], 12, 55, 88, 0);

            $filename .= '_image';
          
        }
        else 
        {	
	        $pdf->Image(COMPASSION_LETTERS_PLUGIN_DIR_PATH . '/assets/images/' . $data['template'] . '.jpg', 0, 0, 210, 297);
			$pdf->rect(100, 0, 75, 36, 'F');
             $pdf->SetXY(100, 0);
            $pdf->MultiCell(73, 6, utf8_decode($user_info),0,'R',false);
	        $pdf->SetXY(12, 55);
	        $pdf->MultiCell(88, 5, utf8_decode(str_replace("\\", "", $data['message'])));
	        $pdf->AddPage();
        }
    
        
       $pdf->Output($directory . '/' . $filename . $extension, 'F');

        return $filename . $extension;
        
		
    }

	
    /**
     * Create a preview image for a specific pdf file
     *
     * @param $path
     * @return array
     */
    public static function preview($path, $destination, $filename) {

        $pages = (strpos($filename, '_image') !== false) ? 2 : 1;
        $im = new Imagick();

        $im->setResolution(300,300);
        $im->readimage("{$path}");

        for($i = 0; $i < $pages; $i++){

            $im->setColorspace(imagick::COLORSPACE_RGB);
            $im->readImage($path."[".$i."]");
            $im->normalizeImage(imagick::COLORSPACE_RGB);
            $im->setImageFormat('jpeg');
            $im->scaleImage(400, 0);
            $im->writeImage($destination . $filename . '-' . $i . '.jpg');
        }

        $im->clear();
        $im->destroy();

        $files = [
            $filename . '-0.jpg'
        ];

        if($pages == 2) {
            $files[] = $filename . '-1.jpg';
        }
        
        return $files;

    }

}