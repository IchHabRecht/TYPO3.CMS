<?php
namespace TYPO3\CMS\Form\Tests\Unit\ContentObject;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Test case for class \TYPO3\CMS\Form\ContentObject\ContentObjectRenderer
 */
class ContentObjectRendererTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Form\ContentObject\ContentObjectRenderer
	 */
	protected $subject;

	/**
	 * @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
	 */
	protected $frontendController;

	protected function setUp() {
		$this->subject = new \TYPO3\CMS\Form\ContentObject\ContentObjectRenderer();
		$this->frontendController = $this->getMock(
			'TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController',
			array('__none'),
			array(),
			'',
			FALSE
		);
		$GLOBALS['TSFE'] = $this->frontendController;
	}

	protected function tearDown() {
		unset($this->subject);
		unset($this->frontendController);
		unset($GLOBALS['TSFE']);
	}

	/**
	 * @param string $type
	 * @param array $configuration
	 *
	 * @test
	 * @dataProvider userFunctionDataProvider
	 */
	public function userFunctionsAreNotEvaluated($type, array $configuration) {
		$content = $this->subject->cObjGetSingle($type, $configuration);
		$this->assertEquals('', $content);
	}

	/**
	 * @return array
	 */
	public function userFunctionDataProvider() {
		$userFunctionClassName = 'TYPO3\\CMS\\Form\\Tests\\Unit\\Fixtures\\UserFunctionFixture';

		return array(
			'USER' => array(
				'USER',
				array(
					'userFunc' => $userFunctionClassName . '->fail',
				)
			),
			'TEXT preUserFunc' => array(
				'TEXT',
				array(
					'preUserFunc' => $userFunctionClassName . '->fail',
				)
			),
			'TEXT postUserFunc' => array(
				'TEXT',
				array(
					'postUserFunc' => $userFunctionClassName . '->fail',
				)
			),
			'TEXT cObject' => array(
				'TEXT',
				array(
					'cObject' => 'USER',
					'cObject.' => array(
						'userFunc' => $userFunctionClassName . '->fail',
					),
				)
			),
		);
	}

}
