<?php
/**
 * Squiz_Sniffs_Classes_ClassFileNameSniff.
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
 * Squiz_Sniffs_Classes_ClassFileNameSniff.
 *
 * Tests that the file name and the name of the class contained within the file
 * match.
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
class Snap_Sniffs_Classes_ClassFileNameSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_CLASS,
                T_INTERFACE,
               );

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $fullPath = $phpcsFile->getFilename();
		if (
			strpos($fullPath, 'tests') !== false
			&& preg_match('/Mock|Stub/', $phpcsFile->getDeclarationName($stackPtr))
		) {
			// allow mocks or stubs to nestle in with tests
			return;
		}

        $tokens   = $phpcsFile->getTokens();
        $decName  = $phpcsFile->findNext(T_STRING, $stackPtr);
        $fileName = basename($fullPath);
        $noext = substr($fileName, 0, strrpos($fileName, '.'));

        if (preg_replace('/^\w+_/', '', $tokens[$decName]['content']) !== $noext) {
            $error = '%s name doesn\'t match filename; expected "%s %s"';
            $data  = array(
                      ucfirst($tokens[$stackPtr]['content']),
                      $tokens[$stackPtr]['content'],
                      $noext,
                     );
            $phpcsFile->addError($error, $stackPtr, 'NoMatch', $data);
        }

    }//end process()


}//end class

?>
