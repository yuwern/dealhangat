<?php
/**
 * CakeResponse 
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake.libs
 * @since         CakePHP(tm) v 2.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
/**
 * CakeResponse is responsible for managing the response text, status and headers of a HTTP response.
 * 
 * By default controllers will use this class to render their response. If you are going to use 
 * a custom response class it should subclass this object in order to ensure compatibility.
 *
 * @package cake.libs
 */
class CakeResponse {

/**
 * Holds HTTP response statuses
 *
 * @var array
 */
	protected $_statusCodes = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Time-out',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Large',
		415 => 'Unsupported Media Type',
		416 => 'Requested range not satisfiable',
		417 => 'Expectation Failed',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Time-out'
	);

/**
 * Holds known mime type mappings
 *
 * @var array
 */
	protected $_mimeTypes = array(
		'ai' => 'application/postscript',
		'bcpio' => 'application/x-bcpio',
		'bin' => 'application/octet-stream',
		'ccad' => 'application/clariscad',
		'cdf' => 'application/x-netcdf',
		'class' => 'application/octet-stream',
		'cpio' => 'application/x-cpio',
		'cpt' => 'application/mac-compactpro',
		'csh' => 'application/x-csh',
		'csv' =>  array('text/csv', 'application/vnd.ms-excel', 'text/plain'),
		'dcr' => 'application/x-director',
		'dir' => 'application/x-director',
		'dms' => 'application/octet-stream',
		'doc' => 'application/msword',
		'drw' => 'application/drafting',
		'dvi' => 'application/x-dvi',
		'dwg' => 'application/acad',
		'dxf' => 'application/dxf',
		'dxr' => 'application/x-director',
		'eot' => 'application/vnd.ms-fontobject',
		'eps' => 'application/postscript',
		'exe' => 'application/octet-stream',
		'ez' => 'application/andrew-inset',
		'flv' => 'video/x-flv',
		'gtar' => 'application/x-gtar',
		'gz' => 'application/x-gzip',
		'bz2' => 'application/x-bzip',
		'7z' => 'application/x-7z-compressed',
		'hdf' => 'application/x-hdf',
		'hqx' => 'application/mac-binhex40',
		'ico' => 'image/vnd.microsoft.icon',
		'ips' => 'application/x-ipscript',
		'ipx' => 'application/x-ipix',
		'js' => 'text/javascript',
		'latex' => 'application/x-latex',
		'lha' => 'application/octet-stream',
		'lsp' => 'application/x-lisp',
		'lzh' => 'application/octet-stream',
		'man' => 'application/x-troff-man',
		'me' => 'application/x-troff-me',
		'mif' => 'application/vnd.mif',
		'ms' => 'application/x-troff-ms',
		'nc' => 'application/x-netcdf',
		'oda' => 'application/oda',
		'otf' => 'font/otf',
		'pdf' => 'application/pdf',
		'pgn' => 'application/x-chess-pgn',
		'pot' => 'application/mspowerpoint',
		'pps' => 'application/mspowerpoint',
		'ppt' => 'application/mspowerpoint',
		'ppz' => 'application/mspowerpoint',
		'pre' => 'application/x-freelance',
		'prt' => 'application/pro_eng',
		'ps' => 'application/postscript',
		'roff' => 'application/x-troff',
		'scm' => 'application/x-lotusscreencam',
		'set' => 'application/set',
		'sh' => 'application/x-sh',
		'shar' => 'application/x-shar',
		'sit' => 'application/x-stuffit',
		'skd' => 'application/x-koan',
		'skm' => 'application/x-koan',
		'skp' => 'application/x-koan',
		'skt' => 'application/x-koan',
		'smi' => 'application/smil',
		'smil' => 'application/smil',
		'sol' => 'application/solids',
		'spl' => 'application/x-futuresplash',
		'src' => 'application/x-wais-source',
		'step' => 'application/STEP',
		'stl' => 'application/SLA',
		'stp' => 'application/STEP',
		'sv4cpio' => 'application/x-sv4cpio',
		'sv4crc' => 'application/x-sv4crc',
		'svg' => 'image/svg+xml',
		'svgz' => 'image/svg+xml',
		'swf' => 'application/x-shockwave-flash',
		't' => 'application/x-troff',
		'tar' => 'application/x-tar',
		'tcl' => 'application/x-tcl',
		'tex' => 'application/x-tex',
		'texi' => 'application/x-texinfo',
		'texinfo' => 'application/x-texinfo',
		'tr' => 'application/x-troff',
		'tsp' => 'application/dsptype',
		'ttf' => 'font/ttf',
		'unv' => 'application/i-deas',
		'ustar' => 'application/x-ustar',
		'vcd' => 'application/x-cdlink',
		'vda' => 'application/vda',
		'xlc' => 'application/vnd.ms-excel',
		'xll' => 'application/vnd.ms-excel',
		'xlm' => 'application/vnd.ms-excel',
		'xls' => 'application/vnd.ms-excel',
		'xlw' => 'application/vnd.ms-excel',
		'zip' => 'application/zip',
		'aif' => 'audio/x-aiff',
		'aifc' => 'audio/x-aiff',
		'aiff' => 'audio/x-aiff',
		'au' => 'audio/basic',
		'kar' => 'audio/midi',
		'mid' => 'audio/midi',
		'midi' => 'audio/midi',
		'mp2' => 'audio/mpeg',
		'mp3' => 'audio/mpeg',
		'mpga' => 'audio/mpeg',
		'ra' => 'audio/x-realaudio',
		'ram' => 'audio/x-pn-realaudio',
		'rm' => 'audio/x-pn-realaudio',
		'rpm' => 'audio/x-pn-realaudio-plugin',
		'snd' => 'audio/basic',
		'tsi' => 'audio/TSP-audio',
		'wav' => 'audio/x-wav',
		'asc' => 'text/plain',
		'c' => 'text/plain',
		'cc' => 'text/plain',
		'css' => 'text/css',
		'etx' => 'text/x-setext',
		'f' => 'text/plain',
		'f90' => 'text/plain',
		'h' => 'text/plain',
		'hh' => 'text/plain',
		'html' => array('text/html', '*/*'),
		'htm' => array('text/html', '*/*'),
		'm' => 'text/plain',
		'rtf' => 'text/rtf',
		'rtx' => 'text/richtext',
		'sgm' => 'text/sgml',
		'sgml' => 'text/sgml',
		'tsv' => 'text/tab-separated-values',
		'tpl' => 'text/template',
		'txt' => 'text/plain',
		'text' => 'text/plain',
		'xml' => array('application/xml', 'text/xml'),
		'avi' => 'video/x-msvideo',
		'fli' => 'video/x-fli',
		'mov' => 'video/quicktime',
		'movie' => 'video/x-sgi-movie',
		'mpe' => 'video/mpeg',
		'mpeg' => 'video/mpeg',
		'mpg' => 'video/mpeg',
		'qt' => 'video/quicktime',
		'viv' => 'video/vnd.vivo',
		'vivo' => 'video/vnd.vivo',
		'gif' => 'image/gif',
		'ief' => 'image/ief',
		'jpe' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'jpg' => 'image/jpeg',
		'pbm' => 'image/x-portable-bitmap',
		'pgm' => 'image/x-portable-graymap',
		'png' => 'image/png',
		'pnm' => 'image/x-portable-anymap',
		'ppm' => 'image/x-portable-pixmap',
		'ras' => 'image/cmu-raster',
		'rgb' => 'image/x-rgb',
		'tif' => 'image/tiff',
		'tiff' => 'image/tiff',
		'xbm' => 'image/x-xbitmap',
		'xpm' => 'image/x-xpixmap',
		'xwd' => 'image/x-xwindowdump',
		'ice' => 'x-conference/x-cooltalk',
		'iges' => 'model/iges',
		'igs' => 'model/iges',
		'mesh' => 'model/mesh',
		'msh' => 'model/mesh',
		'silo' => 'model/mesh',
		'vrml' => 'model/vrml',
		'wrl' => 'model/vrml',
		'mime' => 'www/mime',
		'pdb' => 'chemical/x-pdb',
		'xyz' => 'chemical/x-pdb',
		'javascript' => 'text/javascript',
		'json' => 'application/json',
		'form' => 'application/x-www-form-urlencoded',
		'file' => 'multipart/form-data',
		'xhtml'	=> array('application/xhtml+xml', 'application/xhtml', 'text/xhtml'),
		'xhtml-mobile'	=> 'application/vnd.wap.xhtml+xml',
		'rss' => 'application/rss+xml',
		'atom' => 'application/atom+xml',
		'amf' => 'application/x-amf',
		'wap' => array('text/vnd.wap.wml', 'text/vnd.wap.wmlscript', 'image/vnd.wap.wbmp'),
		'wml' => 'text/vnd.wap.wml',
		'wmlscript' => 'text/vnd.wap.wmlscript',
		'wbmp' => 'image/vnd.wap.wbmp',
// rajesh_04ag02 // 2008-08-12
		'ics'			=> 'text/calendar',
		'vcf'			=> 'text/x-vcard',
		'kml'			=> 'application/vnd.google-earth.kml+xml',
	);

/**
 * Protocol header to send to the client
 *
 * @var string
 */
	protected $_protocol = 'HTTP/1.1';

/**
 * Status code to send to the client
 *
 * @var integer
 */
	protected $_status = 200;

/**
 * Content type to send. This can be an 'extension' that will be transformed using the $_mimetypes array
 * or a complete mime-type
 *
 * @var integer
 */
	protected $_contentType = 'text/html';

/**
 * Buffer list of headers
 *
 * @var array
 */
	protected $_headers = array();

/**
 * Buffer string for response message
 *
 * @var string
 */
	protected $_body = null;

/**
 * The charset the response body is encoded with
 *
 * @var string
 */
	protected $_charset = 'UTF-8';

/**
 * Class constructor
 *
 * @param array $options list of parameters to setup the response. Possible values are:
 *	- body: the rensonse text that should be sent to the client
 *	- status: the HTTP status code to respond with
 *	- type: a complete mime-type string or an extension mapepd in this class
 *	- charset: the charset for the response body
 * @return void
 */
	public function __construct(array $options = array()) {
		if (isset($options['body'])) {
			$this->body($options['body']);
		}
		if (isset($options['status'])) {
			$this->statusCode($options['status']);
		}
		if (isset($options['type'])) {
			$this->type($options['type']);
		}
		if (isset($options['charset'])) {
			$this->charset($options['charset']);
		}
	}

/**
 * Sends the complete response to the client including headers and message body.
 * Will echo out the content in the response body.
 *
 * @return void
 */
	public function send() {
		if (isset($this->_headers['Location']) && $this->_status === 200) {
			$this->statusCode(302);
		}

		$codeMessage = $this->_statusCodes[$this->_status];
		$this->_sendHeader("{$this->_protocol} {$this->_status} {$codeMessage}");
		$this->_sendHeader('Content-Type', "{$this->_contentType}; charset={$this->_charset}");

		foreach ($this->_headers as $header => $value) {
			$this->_sendHeader($header, $value);
		}
		$this->_sendContent($this->_body);
	}

/**
 * Sends a header to the client.
 *
 * @param $name the header name
 * @param $value the header value
 * @return void
 */
	protected function _sendHeader($name, $value = null) {
		if (is_null($value)) {
			header($name);
		} else {
			header("{$name}: {$value}");
		}
	}

/**
 * Sends a content string to the client.
 *
 * @param $content string to send as response body
 * @return void
 */
	protected function _sendContent($content) {
		echo $content;
	}

/**
 * Buffers a header string to be sent
 * Returns the complete list of buffered headers
 *
 * ### Single header
 * e.g `header('Location', 'http://example.com');`
 *
 * ### Multiple headers
 * e.g `header(array('Location' => 'http://example.com', 'X-Extra' => 'My header'));`
 *
 * ### String header
 * e.g `header('WWW-Authenticate: Negotiate');`
 *
 * ### Array of string headers
 * e.g `header(array('WWW-Authenticate: Negotiate', 'Content-type: application/pdf'));`
 *
 * Multiple calls for setting the same header name will have the same effect as setting the header once
 * with the last value sent for it
 *  e.g `header('WWW-Authenticate: Negotiate'); header('WWW-Authenticate: Not-Negotiate');`
 * will have the same effect as only doing `header('WWW-Authenticate: Not-Negotiate');`
 *
 * @param mixed $header. An array of header strings or a single header string
 *	- an assotiative array of "header name" => "header value" is also accepted
 *	- an array of string headers is also accepted
 * @param mixed $value. The header value.
 * @return array list of headers to be sent
 */
	public function header($header = null, $value = null) {
		if (is_null($header)) {
			return $this->_headers;
		}
		if (is_array($header)) {
			foreach ($header as $h => $v) {
				if (is_numeric($h)) {
					$this->header($v);
					continue;
				}
				$this->_headers[$h] = trim($v);
			}
			return $this->_headers;
		}

		if (!is_null($value)) {
			$this->_headers[$header] = $value;
			return $this->_headers;
		}

		list($header, $value) = explode(':', $header, 2);
		$this->_headers[$header] = trim($value);
		return $this->_headers;
	}

/**
 * Buffers the response message to be sent
 * if $content is null the current buffer is returned
 *
 * @param string $content the string message to be sent
 * @return string current message buffer if $content param is passed as null
 */
	public function body($content = null) {
		if (is_null($content)) {
			return $this->_body;
		}
		return $this->_body = $content;
	}

/**
 * Sets the HTTP status code to be sent
 * if $code is null the current code is returned
 *
 * @param integer $code
 * @return integer current status code
 * @throws CakeException When an unknown status code is reached.
 */
	public function statusCode($code = null) {
		if (is_null($code)) {
			return $this->_status;
		}
		if (!isset($this->_statusCodes[$code])) {
			throw new CakeException(__('Unknown status code'));
		}
		return $this->_status = $code;
	}

/**
 * Queries & sets valid HTTP response codes & messages.
 *
 * @param mixed $code If $code is an integer, then the corresponding code/message is
 *        returned if it exists, null if it does not exist. If $code is an array,
 *        then the 'code' and 'message' keys of each nested array are added to the default
 *        HTTP codes. Example:
 *
 *        httpCodes(404); // returns array(404 => 'Not Found')
 *
 *        httpCodes(array(
 *            701 => 'Unicorn Moved',
 *            800 => 'Unexpected Minotaur'
 *        )); // sets these new values, and returns true
 *
 * @return mixed associative array of the HTTP codes as keys, and the message
 *    strings as values, or null of the given $code does not exist.
 */
	public function httpCodes($code = null) {
		if (empty($code)) {
			return $this->_statusCodes;
		}

		if (is_array($code)) {
			$this->_statusCodes = $code + $this->_statusCodes;
			return true;
		}

		if (!isset($this->_statusCodes[$code])) {
			return null;
		}
		return array($code => $this->_statusCodes[$code]);
	}

/**
 * Sets the response content type. It can be either a file extension
 * which will be mapped internally to a mime-type or a string representing a mime-type
 * if $contentType is null the current content type is returned
 * if $contentType is an associative array, it will be stored as a content type definition
 *
 * ### Setting the content type
 *
 * e.g `type('jpg');`
 *
 * ### Returning the current content type
 *
 * e.g `type();`
 *
 * ### Storing a content type definition
 *
 * e.g `type(array('keynote' => 'application/keynote'));`
 *
 * ### Replacing a content type definition
 *
 * e.g `type(array('jpg' => 'text/plain'));`
 *
 * @param string $contentType
 * @return mixed current content type or false if supplied an invalid content type
 */
	public function type($contentType = null) {
		if (is_null($contentType)) {
			return $this->_contentType;
		}
		if (is_array($contentType)) {
			$type = key($contentType);
			$defitition = current($contentType);
			$this->_mimeTypes[$type] = $defitition;
			return $this->_contentType;
		}
		if (isset($this->_mimeTypes[$contentType])) {
			$contentType = $this->_mimeTypes[$contentType];
			$contentType = is_array($contentType) ? current($contentType) : $contentType;
		}
		if (strpos($contentType, '/') === false) {
			return false;
		}
		return $this->_contentType = $contentType;
	}

/**
 * Returns the mime type definition for an alias
 *
 * e.g `getMimeType('pdf'); // returns 'application/pdf'`
 *
 * @param string $alias the content type alias to map
 * @return mixed string mapped mime type or false if $alias is not mapped
 */
	public function getMimeType($alias) {
		if (isset($this->_mimeTypes[$alias])) {
			return $this->_mimeTypes[$alias];
		}
		return false;
	}

/**
 * Maps a content-type back to an alias
 *
 * e.g `mapType('application/pdf'); // returns 'pdf'`
 *
 * @param mixed $type Either a string content type to map, or an array of types.
 * @return mixed Aliases for the types provided.
 */
	public function mapType($ctype) {
		if (is_array($ctype)) {
			return array_map(array($this, 'mapType'), $ctype);
		}
		$keys = array_keys($this->_mimeTypes);
		$count = count($keys);

		foreach ($this->_mimeTypes as $alias => $types) {
			if (is_array($types) && in_array($ctype, $types)) {
				return $alias;
			} elseif (is_string($types) && $types == $ctype) {
				return $alias;
			}
		}
		return null;
	}

/**
 * Sets the response charset
 * if $charset is null the current charset is returned
 *
 * @param string $charset
 * @return string current charset
 */
	public function charset($charset = null) {
		if (is_null($charset)) {
			return $this->_charset;
		}
		return $this->_charset = $charset;
	}

/**
 * Sets the correct headers to instruct the client to not cache the response
 *
 * @return void
 */
	public function disableCache() {
		$this->header(array(
			'Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT',
			'Last-Modified' => gmdate("D, d M Y H:i:s") . " GMT",
			'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
			'Pragma' => 'no-cache'
		));
	}

/**
 * Sets the correct headers to instruct the client to cache the response.
 *
 * @param string $since a valid time since the response text has not been modified
 * @param string $time a valid time for cache expiry
 * @return void
 */
	public function cache($since, $time = '+1 day') {
		if (!is_integer($time)) {
			$time = strtotime($time);
		}
		$this->header(array(
			'Date' => gmdate("D, j M Y G:i:s ", time()) . 'GMT',
			'Last-Modified' => gmdate("D, j M Y G:i:s ", $since) . 'GMT',
			'Expires' => gmdate("D, j M Y H:i:s", $time) . " GMT",
			'Cache-Control' => 'public, max-age=' . ($time - time()),
			'Pragma' => 'cache'
		));
	}

/**
 * Sets the correct output buffering handler to send a compressed response. Responses will
 * be compressed with zlib, if the extension is available.
 *
 * @return boolean false if client does not accept compressed responses or no handler is available, true otherwise
 */
	public function compress() {
		return true;
/*
		$compressionEnabled = ini_get("zlib.output_compression") !== '1' &&
			extension_loaded("zlib") &&
			(strpos(env('HTTP_ACCEPT_ENCODING'), 'gzip') !== false);
		return $compressionEnabled && ob_start('ob_gzhandler');
*/
	}

/**
 * Sets the correct headers to instruct the browser to dowload the response as a file.
 *
 * @param string $filename the name of the file as the browser will download the response
 * @return void
 */
	public function download($filename) {
		$this->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
	}
}