<?php

namespace Fhp\Segment;

use Fhp\DataTypes\Kik;
use Fhp\Deg;
/**
 * Class HNSHA (Signaturabschluss)
 * Segment type: Administration
 *
 * @link: http://www.hbci-zka.de/dokumente/spezifikation_deutsch/fintsv3/FinTS_3.0_Security_Sicherheitsverfahren_HBCI_Rel_20130718_final_version.pdf
 * Section: B.5.2
 *
 * @package Fhp\Segment
 */
class HNSHA extends AbstractSegment
{
    const NAME = 'HNSHA';
    const VERSION = 2;

    /**
     * HNSHA constructor.
     * @param int $segmentNumber
     * @param string $securityControlReference
     * @param string $pin
     */
    public function __construct(
        $segmentNumber,
        $securityControlReference,
        $pin,
		$tan = null
    ) {
		$deg = new Deg();
		$deg->addDataElement($pin);
		if($tan)
			$deg->addDataElement ($tan);
		
        parent::__construct(
            static::NAME,
            $segmentNumber,
            static::VERSION,
            array(
                $securityControlReference,
                '',
                $deg
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return static::NAME;
    }
}
