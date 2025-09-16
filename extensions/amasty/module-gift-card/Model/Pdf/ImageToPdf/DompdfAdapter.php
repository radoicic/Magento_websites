<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */

declare(strict_types=1);

namespace Amasty\GiftCard\Model\Pdf\ImageToPdf;

use Dompdf\Dompdf;
use Dompdf\Options;

class DompdfAdapter implements ImageToPdfAdapterInterface
{
    public const ORIGINAL_DPI = 72;
    public const DEFAULT_FONT = 'Helvetica';

    public function render(string $imageHtml): string
    {
        if (!class_exists(Dompdf::class)) {
            throw new \RuntimeException(
                '\'dompdf/dompdf\' library not found. '
                . 'Please run \'composer require dompdf/dompdf\' command to install it.'
            );
        }

        $options = new Options();
        $options->set('defaultFont', self::DEFAULT_FONT);
        $options->set('dpi', self::ORIGINAL_DPI);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($imageHtml);
        $dompdf->setPaper('A4', 'landscape');

        // Retrieve and adjust CSS
        $css   = $dompdf->getCss();
        $style = $css->create_style();
        // Set zero margin on all sides
        $style->set_prop('margin', '0');
        $css->add_style('html', $style);
        $dompdf->setCss($css);

        $dompdf->render();

        return $dompdf->output();
    }
}
