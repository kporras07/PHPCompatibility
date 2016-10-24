<?php
/**
 * PHPCompatibility_Sniffs_PHP_NewScalarReturnTypeDeclarationsSniff.
 *
 * @category  PHP
 * @package   PHPCompatibility
 * @author    Wim Godden <wim.godden@cu.be>
 */

/**
 * PHPCompatibility_Sniffs_PHP_NewScalarReturnTypeDeclarationsSniff.
 *
 * @category  PHP
 * @package   PHPCompatibility
 * @author    Wim Godden <wim.godden@cu.be>
 */
class PHPCompatibility_Sniffs_PHP_NewScalarReturnTypeDeclarationsSniff extends PHPCompatibility_Sniff
{

    /**
     * A list of new types
     *
     * The array lists : version number with false (not present) or true (present).
     * If's sufficient to list the first version where the keyword appears.
     *
     * @var array(string => array(string => int|string|null))
     */
    protected $newTypes = array (
                                        'int' => array(
                                            '5.6' => false,
                                            '7.0' => true,
                                        ),
                                        'float' => array(
                                            '5.6' => false,
                                            '7.0' => true,
                                        ),
                                        'bool' => array(
                                            '5.6' => false,
                                            '7.0' => true,
                                        ),
                                        'string' => array(
                                            '5.6' => false,
                                            '7.0' => true,
                                        ),

                                        'void' => array(
                                            '7.0' => false,
                                            '7.1' => true,
                                        ),
                                    );


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        if (version_compare(PHP_CodeSniffer::VERSION, '2.3.4') >= 0) {
            return array(T_RETURN_TYPE);
        } else {
            return array();
        }
    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        if (in_array($tokens[$stackPtr]['content'], array_keys($this->newTypes))) {
            $errorInfo = $this->getErrorInfo($tokens[$stackPtr]['content']);

            if ($errorInfo['not_in_version'] !== '') {
                $this->addError($phpcsFile, $stackPtr, $tokens[$stackPtr]['content'], $errorInfo);
            }
        }
    }//end process()


    /**
     * Retrieve the relevant (version) information for the error message.
     *
     * @param string $typeName The return type.
     *
     * @return array
     */
    protected function getErrorInfo($typeName)
    {
        $errorInfo  = array(
            'not_in_version' => '',
        );

        foreach ($this->newTypes[$typeName] as $version => $present) {
            if ($present === false && $this->supportsBelow($version)) {
                $errorInfo['not_in_version'] = $version;
            }
        }

        return $errorInfo;

    }//end getErrorInfo()


    /**
     * Generates the error or warning for this sniff.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the return type token
     *                                        in the token array.
     * @param string               $typeName  The return type.
     * @param array                $errorInfo Array with details about when the
     *                                        return type was not (yet) available.
     *
     * @return void
     */
    protected function addError($phpcsFile, $stackPtr, $typeName, $errorInfo)
    {
        $error     = '%s return type is not present in PHP version %s or earlier';
        $errorCode = $this->stringToErrorCode($typeName) . 'Found';
        $data      = array(
            $typeName,
            $errorInfo['not_in_version'],
        );

        $phpcsFile->addError($error, $stackPtr, $errorCode, $data);

    }//end addError()

}//end class
