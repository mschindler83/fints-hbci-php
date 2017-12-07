<?php

namespace Tests\Fhp\Parser;

use Fhp\Parser\MT940;

class MT940Test extends \PHPUnit_Framework_TestCase
{
	/**
	 * Parser must be able to parse description lines starting with "-".
	 */
	public function testParseDescriptionLines()
	{
		$rawData = [
			":20:STARTUMSE",
			":25:00000000/0221122370",
			":28C:00000/001",
			":60F:C170428EUR6670,54",
			":61:1705030503CR223,72N062NONREF",
			":86:166?00GUTSCHRIFT?109251?20EREF+CCB.122.UE.266455?21SVWZ+Re 17",
			"-H-0005 vom 24.04?22.2017?30DRESDEFF850?31DE00000000000000000000?",
			"32TEST TEST GBR",
			":62F:C170503EUR6894,26",
			"-"
		];

		$parser = new MT940(implode("\r\n", $rawData));
		$result = $parser->parse(MT940::TARGET_ARRAY);
		$this->assertEquals(
            'EREF+CCB.122.UE.266455' . PHP_EOL . 'SVWZ+Re 17-H-0005 vom 24.04' . PHP_EOL . '.2017',
			$result['2017-04-28']['transactions'][0]['description']['description_1']
		);
	}
}
