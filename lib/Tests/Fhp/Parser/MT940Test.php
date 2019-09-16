<?php

namespace Tests\Fhp\Parser;

use Fhp\Parser\MT940;

class MT940Test extends \PHPUnit_Framework_TestCase
{
	public function testYearChange()
	{
		function createTestData($vd, $bd) {
			return <<<EOL
:20:STARTUMSE
:25:57050120/0000211003
:28C:00000/001
:60F:C190101EUR1337,00
:61:${vd}${bd}DR100,00N032NONREF
:86:106?00AUSZAHLUNG?109200?20SVWZ+2016-04-30T06.07.39 Ka?21rte1
:62F:C190101EUR1337,00
EOL;
		}
		$parser = new MT940(preg_replace('/\r?\n/', "\r\n", createTestData('190101', '1231')));
		$tx = $parser->parse(MT940::TARGET_ARRAY)[0]['transactions'][0];
		$this->assertEquals('2019-01-01', $tx['valuta_date']);
		$this->assertEquals('2018-12-31', $tx['booking_date']);

		$parser = new MT940(preg_replace('/\r?\n/', "\r\n", createTestData('181231', '0101')));
		$tx = $parser->parse(MT940::TARGET_ARRAY)[0]['transactions'][0];
		$this->assertEquals('2019-01-01', $tx['booking_date']);
		$this->assertEquals('2018-12-31', $tx['valuta_date']);

		$parser = new MT940(preg_replace('/\r?\n/', "\r\n", createTestData('181231', '1231')));
		$tx = $parser->parse(MT940::TARGET_ARRAY)[0]['transactions'][0];
		$this->assertEquals('2018-12-31', $tx['booking_date']);
		$this->assertEquals('2018-12-31', $tx['valuta_date']);

		$parser = new MT940(preg_replace('/\r?\n/', "\r\n", createTestData('190101', '0101')));
		$tx = $parser->parse(MT940::TARGET_ARRAY)[0]['transactions'][0];
		$this->assertEquals('2019-01-01', $tx['booking_date']);
		$this->assertEquals('2019-01-01', $tx['valuta_date']);
	}


	public function testEarmarkedTransactionsSparkasse()
	{
		$data = <<<EOL
:20:STARTUMSE
:25:57050120/0000211003
:28C:00000/001
:60F:C180302EUR1337,00
:61:1803050305CR1337,00N062NONREF
:86:999PN5477SCHECK-NR. 0000016703074
:62F:C180305EUR1337,00
-+@446@
:20:STARTDISPE
:25:57050120/0000211003
:28C:00000/001
:34F:EURD1337,00
:13:1803051059
:61:1803060305DR14,99NDDTNONREF
:86:999PN5477SCHECK-NR. 0000016703074
:90D:1EUR1337,00
:90C:0EUR1337,00
EOL;

		$parser = new MT940(preg_replace('/\r?\n/', "\r\n", $data));
		$result = $parser->parse(MT940::TARGET_ARRAY);
		$this->assertEquals(1, sizeof($result));
		foreach ($result as $stmt) {
			$this->assertNotNull($stmt['date']);
			$this->assertEquals(':61:1803050305CR1337,00N062NONREF', $stmt['transactions']['0']['turnover_raw']);
			$this->assertEquals(':86:999PN5477SCHECK-NR. 0000016703074', $stmt['transactions']['0']['multi_purpose_raw']);
		}
	}

	public function testParseAtAt()
	{
		// VBKM sent this as single line, \n is just for better readability
		$atat = preg_replace(
			'/\n/',
			'',
			<<<EOL
@@:20:STARTUMS
@@:25:57090000/1076103000
@@:28C:2/1
@@:60F:C180213EUR2323,42
@@:61:1802130213DR3799,45NMSCNONREF
@@:86:177?00HBCI-Euro-Überweisung?10993002?20SVWZ+BIC:BYLADEM1001?21IBA
@@N:DE02120300000000202051?22Datum: 13.02.18 Zeit: 13:48?23UFT 1410
@@861?24Re.Nr. 12345-12312 UR.Nr. 2?25194/2017?30BYLADEM1001?31DE02
@@120300000000202051?32xxxxxxxxxxxxxxxxxxx
@@:61:1802130213DR3406,15NMSCNONREF
@@:86:177?00HBCI-Euro-Überweisung?10993002?20SVWZ+BIC:BYLADEM1001?21IBA
@@N:DE02120300000000202051?22Datum: 13.02.18 Zeit: 13:48?23UFT 1410
@@861?24Re.Nr. 12344-45434 UR.Nr. 2?25193/2017?30BYLADEM1001?31DE02
@@120300000000202051?32xxxxxxxxxxxxxxxxxxx
@@:61:1802130213DR1802,85NMSCNONREF
@@:86:177?00HBCI-Euro-Überweisung?10993002?20SVWZ+BIC:BYLADEM1001?21IBA
@@N:DE02500105170137075030?22Datum: 13.02.18 Zeit: 13:48?23UFT 1410
@@861?24R1800000?30BYLADEM1001?31DE02500105170137075030?32Megahard
@@GmbH
@@:62F:C180213EUR91000,01
@@
@@:20:STARTUMS
@@:25:57090000/1076103000
@@:28C:2/1
@@:60F:C180226EUR191000,01
@@:61:1802260226DR30000,00NMSCNONREF
@@:86:177?00HBCI-Euro-Überweisung?10993002?20SVWZ+BIC:BYLADEM1001?21IBA
@@N:DE02300209000106531065?22Datum: 26.02.18 Zeit: 09:45?23UFT 1410
@@861?24R1801152 1. Teizahlung?30BYLADEM1001?31DE023002090001065310
@@65?32COMPANY GMBH
@@:62F:C180226EUR61000,01
@@
@@:20:STARTUMS
@@:25:57090000/1076103000
@@:28C:2/1
@@:60F:C180227EUR161000,01
@@:61:1802270227DR28046,32NMSCNONREF
@@:86:177?00HBCI-Euro-Überweisung?10993002?20SVWZ+BIC:BYLADEM1001?21IBA
@@N:DE02300209000106531065?22Datum: 27.02.18 Zeit: 14:22?23UFT 1410
@@861?24R1801152 Restzahlung?30BYLADEM1001?31DE02300209000106531065
@@?32COMPANY GMBH
@@:62F:C180227EUR32953,69
@@
@@:20:STARTUMS
@@:25:57090000/1076103000
@@:28C:2/1
@@:60F:C180228EUR32953,69
@@:61:1802300228DR2100,00NMSCNONREF
@@:86:005?00Zinsen?10990301?20Darl. Abrechnung 1000000000?21Akt. Sollzi
@@nssatz   1,4000%
@@:61:1802280228DR20000,00NMSCNONREF
@@:86:177?00HBCI-Euro-Überweisung?10993002?20SVWZ+BIC:BYLADEM1001?21IBA
@@N:DE02200505501015871393?22Datum: 28.02.18 Zeit: 09:36?23UFT 1410
@@861?24Umbuchung?30BYLADEM1001?31DE02200505501015871393?32Xxxxxxxx
@@xx GmbH
@@:61:1802300228DR13,21NMSCNONREF
@@:86:005?00Zinsen/Kontoführung?10990197?20Abrechnung vom  28.02.2018
@@:62F:C180228EUR12953,69
@@";
EOL
);
		$parser = new MT940($atat);
		$result = $parser->parse(MT940::TARGET_ARRAY);
		$this->assertEquals(4, sizeof($result));
		$this->assertEquals(161000.01, $result[2]['start_balance']['amount']);
		$this->assertEquals(
			'Darl. Abrechnung 1000000000Akt. Sollzinssatz 1,4000%',
			$result[3]['transactions'][0]['description']['description_1']
		);
	}

	/**
	 * Parser must be able to parse description lines starting with "-".
	 */
	public function testParseDescriptionLines()
	{
		$rawData = [
			':20:STARTUMSE',
			':25:00000000/0221122370',
			':28C:00000/001',
			':60F:C170428EUR6670,54',
			':61:1705030503CR223,72N062NONREF',
			':86:166?00GUTSCHRIFT?109251?20EREF+CCB.122.UE.266455?21SVWZ+Re 17',
			'-H-0005 vom 24.04?22.2017?30DRESDEFF850?31DE00000000000000000000?',
			'32TEST TEST GBR',
			':62F:C170503EUR6894,26',
			'-',
			':20:STARTUMSE',
			':25:00000000/0221122370',
			':28C:00000/001',
			':60M:C170428EUR6894,26',
			':61:1705030503CR3105.74N062NONREF',
			':86:166?00GUTSCHRIFT?109251?20EREF+CCB.122.UE.266455?21SVWZ+Re 17',
			'-H-0005 vom 24.04?22.2017?30DRESDEFF850?31DE00000000000000000000?',
			'32TEST TEST GBR',
			':62F:C170503EUR10000,00',
			'-',
			':20:STARTUMSE',
			':25:00000000/0221122370',
			':28C:00000/001',
			':60F:C170429EUR10000,00',
			':61:1705030503CR100,00N062NONREF',
			':86:166?00GUTSCHRIFT?109251?20EREF+CCB.122.UE.266455?21SVWZ+Re 17',
			'-H-0005 vom 24.04?22.2017?30DRESDEFF850?31DE00000000000000000000?',
			'32TEST TEST GBR',
			':62F:C170503EUR10100,00',
			'-'
		];

		$parser = new MT940(implode("\r\n", $rawData));
		$result = $parser->parse(MT940::TARGET_ARRAY);
		$this->assertEquals(
			'EREF+CCB.122.UE.266455SVWZ+Re 17-H-0005 vom 24.04.2017',
			$result[0]['transactions'][0]['description']['description_1']
		);
		$this->assertEquals(
			'166',
			$result[0]['transactions'][0]['transaction_code']
		);
		$this->assertEquals(
			'223.72',
			$result[0]['transactions'][0]['amount']
		);
		$this->assertEquals(
			'2017-04-29',
						$result[2]['date']
				);
		$this->assertEquals(
			10000.00,
			$result[2]['start_balance']['amount']
		);
	}
}
