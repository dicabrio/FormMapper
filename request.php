<?php
/**
 *	class Request
 *
 *	Use this request object for getting stuff from your superglobals
 *	By using this object your are certain that your data is or can be filtered
 *	from bad input
 */
class Request
{

	const POST = 'post';

	const GET = 'get';

	private $m_aGet;

	private $m_aPost;

	private $m_aRequest;
	
	private $m_aFiles;

	private $m_aCookie;

	private $m_bStrict;

	private static $m_oInstance;

	private function __construct() {
		// Import variables
		//TODO do some cleanup
		$this->m_aGet 		= $_GET;
		$this->m_aPost 		= $_POST;
		$this->m_aRequest 	= $_REQUEST;
		$this->m_aCookie 	= $_COOKIE;
		$this->m_aFiles		= $_FILES;
	}

	/**
	 *	Singleton::getInstance()
	 *
	 *	the getInstance() method returns a single instance of the object
	 * @return Request
	 */
	public static function getInstance() {
		if( !isset( self::$m_oInstance ) ) {
			$object = __CLASS__;
			self::$m_oInstance = new $object;
		}
			
		return self::$m_oInstance;
	}

	/**
	 * Get values from the $_POST variable
	 *
	 * @param string $p_sParameter
	 * $param mixed $overwrite
	 * @return mixed
	 */
	public function post($p_sParameter, $overwrite=null) {
		return $this->request($this->m_aPost, $p_sParameter, $overwrite);
	}

	/**
	 * Get a values from the $_GET variable
	 *
	 * @param string $p_sParameter
	 * $param mixed $overwrite
	 * @return mixed
	 */
	public function get($p_sParameter, $overwrite=null) {
		return $this->request($this->m_aGet, $p_sParameter, $overwrite);
	}

	/**
	 * Get values from the $_COOKIE variable
	 *
	 * @param string $param
	 * @param mixed $overwrite
	 * @return mixed
	 */
	public function cookie($param, $overwrite=null) {
		return $this->request($this->m_aCookie, $param, $overwrite);
	}

	/**
	 * Method for getting from request/post/get/cookie
	 *
	 * @param unknown_type $pool
	 * @param unknown_type $param
	 * @param unknown_type $overwrite
	 * @return unknown
	 */
	private function request($pool, $param, $overwrite) {
		if ($overwrite !== null) {
			return $overwrite;
		}

		if (isset($pool[$param])) {
			return $pool[$param];
		}

		return '';
	}

	/**
	 * Enter description here...
	 *
	 * @param string $p_sFieldname
	 * @return mixed
	 */
	public function files($psFieldname=null) {
		if ($psFieldname === null) {
			return $_FILES;
		}
		return Util::arrayPath($_FILES, $psFieldname);
	}

	public function requests() {
		return $this->m_aRequest;
	}

	public function cookies() {
		return $this->m_aCookie;
	}

	public function posts() {
		return $this->m_aPost;
	}

	public function gets() {
		return $this->m_aGet;
	}

	/**
	 * returns the request method
	 * POST or GET
	 *
	 * @return string
	 */
	public static function method() {
		return strtolower($_SERVER['REQUEST_METHOD']);
	}

	public function setStrictCleanUp($p_bStrict) {
		$this->m_bStrict = $p_bStrict;
	}

	public function emptyRequest() {
		unset($this->m_aRequest);
	}

	public static function cleanUp() {
		$this->m_aGet = $this->cleanUserInput($this->m_aGet, $this->m_bStrict);
		$this->m_aPost = $this->cleanUserInput($this->m_aPost, $this->m_bStrict);
		$this->m_aRequest = $this->cleanUserInput($this->m_aRequest, $this->m_bStrict);
		$this->m_aCookie = $this->cleanUserInput($this->m_aCookie, $this->m_bStrict);
	}

	/**
	 *	function CleanUserInput
	 *
	 *	This function will walk recursivly through the given array and check for
	 *	stuff that should not be there like <script>javascript stuff</script>
	 *	to avoid exploiting your script
	 */
	private function cleanUserInput( &$p_vIn, $p_bStrict = false )
	{
		if( is_array( $p_vIn ) )
		{
			foreach( $p_vIn as $vKey => $vValue )
			{
				$p_vIn[$vKey] = $this->cleanUserInput( $vValue, $p_bStrict );
			}
		}
		elseif( !is_numeric( $p_vIn ) )
		{
			$p_vIn = $this->retreiveValue( $p_vIn, $p_bStrict );
		}

		return $p_vIn;
	}

	function retreiveValue( $p_sIn, $p_bStrict = false )
	{
		//$p_sIn = is_string($in)?urldecode($in):$p_sIn;
		$sIn = ltrim(trim($p_sIn));
		$sIn = str_replace( "%20", "_", $sIn );
		$sIn = preg_replace( "'<script[^>]*?>.*?</script>'si", "", $sIn );

		if( $p_bStrict )
		{
			$sIn = preg_replace( "'<head[^>]*?>.*?</head>'si", "", $sIn );
			$sIn = preg_replace( '/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2 (\1)', $sIn );

			$aTrans = array_flip( get_html_translation_table( HTML_ENTITIES ) );

			//strip nonbreaking space, strip php tags, strip html tags, convert html entites, strip extrawhite space
			$aSearchClean = array( "%&nbsp;%i", "%<\?.*\?>%Usi","%<[\/]*[^<>]*>%Usi", "%(\&[a-zA-Z0-9\#]+;)%es", "%\s+%");
			$aReplaceClean = array( " ", "","", "strtr('\\1',\$trans)", " " );
			$sIn = preg_replace( $aSearchClean, $aReplaceClean, $sIn );
			// Remove specialcharacters
			//$sIn = $this->stripSpecialChars($sIn);
		}

		$sIn = trim($sIn);
		return ($sIn);
	}


	function stripSpecialChars( $p_sIn )
	{
		$aSpecialChars = array( '&','#','$','%','^','*','~',']','[','{','}','|',';',':','<','>',',','?' );
		$p_sIn = str_replace( $aSpecialChars, "", $p_sIn );
		return $p_sIn;
	}
}
?>