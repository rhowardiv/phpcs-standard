<?php
/**
 * Squiz_Sniffs_Classes_ValidClassNameSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2011 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Squiz_Sniffs_Classes_ValidClassNameSniff.
 *
 * Ensures classes are in camel caps, and the first letter is capitalised
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2011 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.3.3
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Snap_Sniffs_Classes_ValidClassNameSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
	public function register() {
		return array(
				T_CLASS,
				T_INTERFACE,
			   );

	}


	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The current file being processed.
	 * @param int                  $stackPtr  The position of the current token in the
	 *                                        stack passed in $tokens.
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		if (isset($tokens[$stackPtr]['scope_opener']) === false) {
			$error = 'Possible parse error: %s missing opening or closing brace';
			$data  = array($tokens[$stackPtr]['content']);
			$phpcsFile->addWarning($error, $stackPtr, 'MissingBrace', $data);
			return;
		}

		$name = $phpcsFile->getDeclarationName($stackPtr);

		// Check for camel caps format.
		// ... but allow underscores for filepathing
		$valid = PHP_CodeSniffer::isCamelCaps(preg_replace('/^\w+_/', '', $name), true, true, false);
		if ($valid === false) {
			$type  = ucfirst($tokens[$stackPtr]['content']);
			$error = '%s name "%s" is not in camel caps format';
			$data  = array(
				$type,
				$name,
			);
			$phpcsFile->addError($error, $stackPtr, 'NotCamelCaps', $data);
		}

		$this->checkBraceSpacing($phpcsFile, $stackPtr);
		$this->checkBraceLine($phpcsFile, $stackPtr);
	}

	/**
	 * Check the brace that opens the class is properly positioned
	 * @param PHP_CodeSniffer_File $phpcsFile
	 * @param int $stackPtr
	 */
	protected function checkBraceSpacing(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();
		$opener = $tokens[$stackPtr]['scope_opener'];
		$i = $opener - 1;
		$space_count = 0;
		while ($tokens[$i]['content'] === ' ') {
			$space_count++;
			$i--;
		}
		if ($space_count !== 1) {
			$phpcsFile->addError(
				'1 space expected before opening brace; %d found',
				$stackPtr,
				'BadSpaceBeforeClassBrace',
				array($space_count)
			);
		}
	}

	/**
	 * Check the brace is on the same line as the class/interface declaration
	 * @param PHP_CodeSniffer_File $phpcsFile
	 * @param int $stackPtr
	 */
	protected function checkBraceLine(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();
		$opener = $tokens[$stackPtr]['scope_opener'];
		if ($tokens[$stackPtr]['line'] != $tokens[$opener]['line']) {
			$phpcsFile->addError(
				'Opening brace must be on same line as class declaration',
				$stackPtr,
				'ClassBraceWrongLine'
			);
		}
	}
}
