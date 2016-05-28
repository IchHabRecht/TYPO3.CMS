<?php
namespace TYPO3\CMS\Form\ContentObject;

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

use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;

/**
 * Reduced set of ContentObjectRenderer to disable
 * user functions and processing records with TypoScript.
 */
class ContentObjectRenderer extends \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer {

	/**
	 * Class names for accordant content objects
	 *
	 * @var array
	 */
	protected $allowedContentObjects = array(
		'TEXT',
		'CASE',
		'COBJ_ARRAY',
		'COA',
		'COA_INT',
		'LOAD_REGISTER',
		'RESTORE_REGISTER'
	);

	/**
	 * Limited set of allowed stdWrap types
	 *
	 * @var array
	 */
	protected $allowedStdWrapTypes = array(
		'array',
		'boolean',
		'string',
		'integer',
		'cObject',
		'getText',
		'fieldName',
		'stdWrap',
		'objectpath',
		'dateconf',
		'strftimeconf',
		'parameters',
		'crop',
		'wrap'
	);

	/**
	 * Limited set of allowed keys to be used in getData
	 *
	 * @var array
	 * @see getData()
	 */
	protected $allowedGetDataKeys = array(
		'gp',
		'field',
		'register',
		'level',
		'leveltitle',
		'levelmedia',
		'leveluid',
		'levelfield',
		'fullrootline',
		'date',
		'page',
		'current',
		'lll'
	);

	/**
	 * Initializes the reduced feature set
	 */
	public function __construct() {
		foreach ($this->stdWrapOrder as $propertyName => $propertyValue) {
			if (in_array($propertyValue, $this->allowedStdWrapTypes, true)) {
				continue;
			}
			$propertyNamePlain = rtrim($propertyName, '.');
			$propertyNameSubSet = $propertyNamePlain . '.';
			if (isset($this->stdWrapOrder[$propertyNamePlain])) {
				unset($this->stdWrapOrder[$propertyNamePlain]);
			}
			if (isset($this->stdWrapOrder[$propertyNameSubSet])) {
				unset($this->stdWrapOrder[$propertyNameSubSet]);
			}
		}

		foreach ($this->contentObjectClassMapping as $contentObjectName => $contentObjectClass) {
			if (!in_array($contentObjectName, $this->allowedContentObjects, true)) {
				unset($this->contentObjectClassMapping[$contentObjectName]);
			}
		}
	}

	/**
	 * Disables hooks
	 *
	 * @param array $data
	 * @param string $table
	 */
	public function start($data, $table = '') {
		parent::start($data, $table);
		$this->cObjHookObjectsRegistry = array();
		$this->stdWrapHookObjects = array();
	}

	/**
	 * Get a content object from the reduced set of allowedContentObjects or an EmptyContentObject
	 *
	 * @param string $name
	 * @return EmptyContentObject|AbstractContentObject
	 */
	public function getContentObject($name) {
		$contentObject = parent::getContentObject($name);
		if ($contentObject !== NULL) {
			return $contentObject;
		}
		return new EmptyContentObject($this);
	}

	/**
	 * Disables IMG_RESOURCE hooks
	 */
	protected function getGetImgResourceHookObjects() {
		return;
	}

	/**
	 * Disables userFunc
	 *
	 * @param string $funcName
	 * @param array $conf
	 * @param string $content
	 * @return string
	 */
	public function callUserFunction($funcName, $conf, $content) {
		return $content;
	}

	/**
	 * Get data from the reduced set of allowedGetDataKeys only
	 *
	 * @param string $string
	 * @param array|NULL $fieldArray
	 * @return string
	 */
	public function getData($string, $fieldArray = NULL) {
		$result = '';
		$sections = explode('//', $string);
		foreach ($sections as $section) {
			$parts = explode(':', $section, 2);
			if (!in_array($parts[0], $this->allowedGetDataKeys, true)) {
				continue;
			}
			$result = parent::getData($section, $fieldArray);
			if ($result !== '') {
				break;
			}
		}
		return $result;
	}

}
