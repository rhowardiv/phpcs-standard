<?php
/**
 * Makes sure there are spaces between the concatenation operator (.) and
 * the strings being concatenated.
 * @name Snap_Sniffs_Strings_ConcatenationSpacingSniff
 */
class Snap_Sniffs_Strings_ConcatenationSpacingSniff implements PHP_CodeSniffer_Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 * @return array
	 */
	public function register() {
		return array(T_STRING_CONCAT);
	}

	/**
	 * Run whenever a '.' is encountered
	 *
	 * @param PHP_CodeSniffer_File $file The file being scanned.
	 * @param int $stack_ix The position of the current token in the stack
	 */
	public function process(PHP_CodeSniffer_File $file, $stack_ix) {
		$tokens = $file->getTokens();
		if (
			$tokens[($stack_ix - 1)]['code'] !== T_WHITESPACE
			|| $tokens[($stack_ix + 1)]['code'] !== T_WHITESPACE
		) {
			$file->addError('Concat operator must be surrounded by spaces', $stack_ix, 'ConcatUnspaced');
		}
	}
}
