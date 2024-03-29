<?php
/**
 * Security Component
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
 * @package       cake.libs.controller.components
 * @since         CakePHP(tm) v 0.10.8.2156
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::import('Core', 'String', false);
App::import('Core', 'Security', false);

/**
 * SecurityComponent
 *
 * @package       cake.libs.controller.components
 * @link http://book.cakephp.org/view/1296/Security-Component
 */
class SecurityComponent extends Component {

/**
 * The controller method that will be called if this request is black-hole'd
 *
 * @var string
 * @access public
 */
	public $blackHoleCallback = null;

/**
 * List of controller actions for which a POST request is required
 *
 * @var array
 * @access public
 * @see SecurityComponent::requirePost()
 */
	public $requirePost = array();

/**
 * List of controller actions for which a GET request is required
 *
 * @var array
 * @access public
 * @see SecurityComponent::requireGet()
 */
	public $requireGet = array();

/**
 * List of controller actions for which a PUT request is required
 *
 * @var array
 * @access public
 * @see SecurityComponent::requirePut()
 */
	public $requirePut = array();

/**
 * List of controller actions for which a DELETE request is required
 *
 * @var array
 * @access public
 * @see SecurityComponent::requireDelete()
 */
	public $requireDelete = array();

/**
 * List of actions that require an SSL-secured connection
 *
 * @var array
 * @access public
 * @see SecurityComponent::requireSecure()
 */
	public $requireSecure = array();

/**
 * List of actions that require a valid authentication key
 *
 * @var array
 * @access public
 * @see SecurityComponent::requireAuth()
 */
	public $requireAuth = array();

/**
 * List of actions that require an HTTP-authenticated login (basic or digest)
 *
 * @var array
 * @access public
 * @see SecurityComponent::requireLogin()
 */
	public $requireLogin = array();

/**
 * Login options for SecurityComponent::requireLogin()
 *
 * @var array
 * @access public
 * @see SecurityComponent::requireLogin()
 */
	public $loginOptions = array('type' => '', 'prompt' => null);

/**
 * An associative array of usernames/passwords used for HTTP-authenticated logins.
 *
 * @var array
 * @access public
 * @see SecurityComponent::requireLogin()
 */
	public $loginUsers = array();

/**
 * Controllers from which actions of the current controller are allowed to receive
 * requests.
 *
 * @var array
 * @access public
 * @see SecurityComponent::requireAuth()
 */
	public $allowedControllers = array();

/**
 * Actions from which actions of the current controller are allowed to receive
 * requests.
 *
 * @var array
 * @access public
 * @see SecurityComponent::requireAuth()
 */
	public $allowedActions = array();

/**
 * Form fields to disable
 *
 * @var array
 * @access public
 */
	public $disabledFields = array();

/**
 * Whether to validate POST data.  Set to false to disable for data coming from 3rd party
 * services, etc.
 *
 * @var boolean
 * @access public
 */
	public $validatePost = true;

/**
 * Whether to use CSRF protected forms.  Set to false to disable CSRF protection on forms.
 *
 * @var boolean
 * @see http://www.owasp.org/index.php/Cross-Site_Request_Forgery_(CSRF)
 * @see SecurityComponent::$csrfExpires
 */
	public $csrfCheck = false;

/**
 * The duration from when a CSRF token is created that it will expire on.
 * Each form/page request will generate a new token that can only be submitted once unless 
 * it expires.  Can be any value compatible with strtotime()
 *
 * @var string
 */
	public $csrfExpires = '+30 minutes';

/**
 * Controls whether or not CSRF tokens are use and burn.  Set to false to not generate
 * new tokens on each request.  One token will be reused until it expires. This reduces
 * the chances of users getting invalid requests because of token consumption.
 * It has the side effect of making CSRF less secure, as tokens are reusable.
 *
 * @var boolean
 */
	public $csrfUseOnce = true;

/**
 * Other components used by the Security component
 *
 * @var array
 * @access public
 */
	public $components = array('Session');

/**
 * Holds the current action of the controller
 *
 * @var string
 */
	protected $_action = null;

/**
 * Request object
 *
 * @var CakeRequest
 */
	public $request;

/**
 * Component startup. All security checking happens here.
 *
 * @param object $controller Instantiating controller
 * @return void
 */
	public function startup($controller) {
		$this->request = $controller->request;
		$this->_action = strtolower($this->request->params['action']);
		$this->_methodsRequired($controller);
		$this->_secureRequired($controller);
		$this->_authRequired($controller);
		$this->_loginRequired($controller);

		$isPost = ($this->request->is('post') || $this->request->is('put'));
		$isRequestAction = (
			!isset($controller->request->params['requested']) ||
			$controller->request->params['requested'] != 1
		);

		if ($isPost && $isRequestAction && $this->validatePost) {
			if ($this->_validatePost($controller) === false) {
				return $this->blackHole($controller, 'auth');
			}
		}
		if ($isPost && $this->csrfCheck) {
			if ($this->_validateCsrf($controller) === false) {
				return $this->blackHole($controller, 'csrf');
			}
		}
		$this->_generateToken($controller);
	}

/**
 * Sets the actions that require a POST request, or empty for all actions
 *
 * @return void
 * @link http://book.cakephp.org/view/1299/requirePost
 */
	public function requirePost() {
		$args = func_get_args();
		$this->_requireMethod('Post', $args);
	}

/**
 * Sets the actions that require a GET request, or empty for all actions
 *
 * @return void
 */
	public function requireGet() {
		$args = func_get_args();
		$this->_requireMethod('Get', $args);
	}

/**
 * Sets the actions that require a PUT request, or empty for all actions
 *
 * @return void
 */
	public function requirePut() {
		$args = func_get_args();
		$this->_requireMethod('Put', $args);
	}

/**
 * Sets the actions that require a DELETE request, or empty for all actions
 *
 * @return void
 */
	public function requireDelete() {
		$args = func_get_args();
		$this->_requireMethod('Delete', $args);
	}

/**
 * Sets the actions that require a request that is SSL-secured, or empty for all actions
 *
 * @return void
 * @link http://book.cakephp.org/view/1300/requireSecure
 */
	public function requireSecure() {
		$args = func_get_args();
		$this->_requireMethod('Secure', $args);
	}

/**
 * Sets the actions that require an authenticated request, or empty for all actions
 *
 * @return void
 * @link http://book.cakephp.org/view/1301/requireAuth
 */
	public function requireAuth() {
		$args = func_get_args();
		$this->_requireMethod('Auth', $args);
	}

/**
 * Sets the actions that require an HTTP-authenticated request, or empty for all actions
 *
 * @return void
 * @link http://book.cakephp.org/view/1302/requireLogin
 */
	public function requireLogin() {
		$args = func_get_args();
		$base = $this->loginOptions;

		foreach ($args as $i => $arg) {
			if (is_array($arg)) {
				$this->loginOptions = $arg;
				unset($args[$i]);
			}
		}
		$this->loginOptions = array_merge($base, $this->loginOptions);
		$this->_requireMethod('Login', $args);
		if (isset($this->loginOptions['users'])) {
			$this->loginUsers = $this->loginOptions['users'];
		}
	}

/**
 * Attempts to validate the login credentials for an HTTP-authenticated request
 *
 * @param string $type Either 'basic', 'digest', or null. If null/empty, will try both.
 * @return mixed If successful, returns an array with login name and password, otherwise null.
 * @link http://book.cakephp.org/view/1303/loginCredentials-string-type
 */
	public function loginCredentials($type = null) {
		switch (strtolower($type)) {
			case 'basic':
				$login = array('username' => env('PHP_AUTH_USER'), 'password' => env('PHP_AUTH_PW'));
				if (!empty($login['username'])) {
					return $login;
				}
			break;
			case 'digest':
			default:
				$digest = null;

				if (version_compare(PHP_VERSION, '5.1') != -1) {
					$digest = env('PHP_AUTH_DIGEST');
				} elseif (function_exists('apache_request_headers')) {
					$headers = apache_request_headers();
					if (isset($headers['Authorization']) && !empty($headers['Authorization']) && substr($headers['Authorization'], 0, 7) == 'Digest ') {
						$digest = substr($headers['Authorization'], 7);
					}
				} else {
					// Server doesn't support digest-auth headers
					trigger_error(__('SecurityComponent::loginCredentials() - Server does not support digest authentication'), E_USER_WARNING);
				}

				if (!empty($digest)) {
					return $this->parseDigestAuthData($digest);
				}
			break;
		}
		return null;
	}

/**
 * Generates the text of an HTTP-authentication request header from an array of options.
 *
 * @param array $options Set of options for header
 * @return string HTTP-authentication request header
 * @link http://book.cakephp.org/view/1304/loginRequest-array-options
 */
	public function loginRequest($options = array()) {
		$options = array_merge($this->loginOptions, $options);
		$this->_setLoginDefaults($options);
		$auth = 'WWW-Authenticate: ' . ucfirst($options['type']);
		$out = array('realm="' . $options['realm'] . '"');

		if (strtolower($options['type']) == 'digest') {
			$out[] = 'qop="auth"';
			$out[] = 'nonce="' . uniqid("") . '"';
			$out[] = 'opaque="' . md5($options['realm']) . '"';
		}

		return $auth . ' ' . implode(',', $out);
	}

/**
 * Parses an HTTP digest authentication response, and returns an array of the data, or null on failure.
 *
 * @param string $digest Digest authentication response
 * @return array Digest authentication parameters
 * @link http://book.cakephp.org/view/1305/parseDigestAuthData-string-digest
 */
	public function parseDigestAuthData($digest) {
		if (substr($digest, 0, 7) == 'Digest ') {
			$digest = substr($digest, 7);
		}
		$keys = array();
		$match = array();
		$req = array('nonce' => 1, 'nc' => 1, 'cnonce' => 1, 'qop' => 1, 'username' => 1, 'uri' => 1, 'response' => 1);
		preg_match_all('/(\w+)=([\'"]?)([a-zA-Z0-9@=.\/_-]+)\2/', $digest, $match, PREG_SET_ORDER);

		foreach ($match as $i) {
			$keys[$i[1]] = $i[3];
			unset($req[$i[1]]);
		}

		if (empty($req)) {
			return $keys;
		}
		return null;
	}

/**
 * Generates a hash to be compared with an HTTP digest-authenticated response
 *
 * @param array $data HTTP digest response data, as parsed by SecurityComponent::parseDigestAuthData()
 * @return string Digest authentication hash
 * @access public
 * @see SecurityComponent::parseDigestAuthData()
 * @link http://book.cakephp.org/view/1306/generateDigestResponseHash-array-data
 */
	function generateDigestResponseHash($data) {
		return md5(
			md5($data['username'] . ':' . $this->loginOptions['realm'] . ':' . $this->loginUsers[$data['username']]) .
			':' . $data['nonce'] . ':' . $data['nc'] . ':' . $data['cnonce'] . ':' . $data['qop'] . ':' .
			md5(env('REQUEST_METHOD') . ':' . $data['uri'])
		);
	}

/**
 * Black-hole an invalid request with a 404 error or custom callback. If SecurityComponent::$blackHoleCallback
 * is specified, it will use this callback by executing the method indicated in $error
 *
 * @param object $controller Instantiating controller
 * @param string $error Error method
 * @return mixed If specified, controller blackHoleCallback's response, or no return otherwise
 * @access public
 * @see SecurityComponent::$blackHoleCallback
 * @link http://book.cakephp.org/view/1307/blackHole-object-controller-string-error
 */
	function blackHole($controller, $error = '') {
		if ($this->blackHoleCallback == null) {
			$code = 404;
			if ($error == 'login') {
				$code = 401;
				$controller->header($this->loginRequest());
			}
			return $controller->redirect(null, $code, true);
		} else {
			return $this->_callback($controller, $this->blackHoleCallback, array($error));
		}
	}

/**
 * Sets the actions that require a $method HTTP request, or empty for all actions
 *
 * @param string $method The HTTP method to assign controller actions to
 * @param array $actions Controller actions to set the required HTTP method to.
 * @return void
 */
	protected function _requireMethod($method, $actions = array()) {
		if (isset($actions[0]) && is_array($actions[0])) {
			$actions = $actions[0];
		}
		$this->{'require' . $method} = (empty($actions)) ? array('*'): $actions;
	}

/**
 * Check if HTTP methods are required
 *
 * @param object $controller Instantiating controller
 * @return bool true if $method is required
 */
	protected function _methodsRequired($controller) {
		foreach (array('Post', 'Get', 'Put', 'Delete') as $method) {
			$property = 'require' . $method;
			if (is_array($this->$property) && !empty($this->$property)) {
				$require = array_map('strtolower', $this->$property);
				if (in_array($this->_action, $require) || $this->$property == array('*')) {
					if (!$this->request->is(strtolower($method))) {
						if (!$this->blackHole($controller, strtolower($method))) {
							return null;
						}
					}
				}
			}
		}
		return true;
	}

/**
 * Check if access requires secure connection
 *
 * @param object $controller Instantiating controller
 * @return bool true if secure connection required
 */
	protected function _secureRequired($controller) {
		if (is_array($this->requireSecure) && !empty($this->requireSecure)) {
			$requireSecure = array_map('strtolower', $this->requireSecure);

			if (in_array($this->_action, $requireSecure) || $this->requireSecure == array('*')) {
				if (!$this->request->is('ssl')) {
					if (!$this->blackHole($controller, 'secure')) {
						return null;
					}
				}
			}
		}
		return true;
	}

/**
 * Check if authentication is required
 *
 * @param object $controller Instantiating controller
 * @return bool true if authentication required
 */
	protected function _authRequired($controller) {
		if (is_array($this->requireAuth) && !empty($this->requireAuth) && !empty($this->request->data)) {
			$requireAuth = array_map('strtolower', $this->requireAuth);

			if (in_array($this->request->params['action'], $requireAuth) || $this->requireAuth == array('*')) {
				if (!isset($controller->request->data['_Token'] )) {
					if (!$this->blackHole($controller, 'auth')) {
						return null;
					}
				}

				if ($this->Session->check('_Token')) {
					$tData = $this->Session->read('_Token');

					if (!empty($tData['allowedControllers']) && !in_array($this->request->params['controller'], $tData['allowedControllers']) || !empty($tData['allowedActions']) && !in_array($this->request->params['action'], $tData['allowedActions'])) {
						if (!$this->blackHole($controller, 'auth')) {
							return null;
						}
					}
				} else {
					if (!$this->blackHole($controller, 'auth')) {
						return null;
					}
				}
			}
		}
		return true;
	}

/**
 * Check if login is required
 *
 * @param object $controller Instantiating controller
 * @return bool true if login is required
 */
	protected function _loginRequired($controller) {
		if (is_array($this->requireLogin) && !empty($this->requireLogin)) {
			$requireLogin = $this->requireLogin;

			if (in_array($this->_action, $this->requireLogin) || $this->requireLogin == array('*')) {
				$login = $this->loginCredentials($this->loginOptions['type']);

				if ($login == null) {
					$controller->header($this->loginRequest());

					if (!empty($this->loginOptions['prompt'])) {
						$this->_callback($controller, $this->loginOptions['prompt']);
					} else {
						$this->blackHole($controller, 'login');
					}
				} else {
					if (isset($this->loginOptions['login'])) {
						$this->_callback($controller, $this->loginOptions['login'], array($login));
					} else {
						if (strtolower($this->loginOptions['type']) == 'digest') {
							if ($login && isset($this->loginUsers[$login['username']])) {
								if ($login['response'] == $this->generateDigestResponseHash($login)) {
									return true;
								}
							}
							$this->blackHole($controller, 'login');
						} else {
							if (
								!(in_array($login['username'], array_keys($this->loginUsers)) &&
								$this->loginUsers[$login['username']] == $login['password'])
							) {
								$this->blackHole($controller, 'login');
							}
						}
					}
				}
			}
		}
		return true;
	}

/**
 * Validate submitted form
 *
 * @param object $controller Instantiating controller
 * @return bool true if submitted form is valid
 */
	protected function _validatePost($controller) {
		if (empty($controller->request->data)) {
			return true;
		}
		$data = $controller->request->data;

		if (!isset($data['_Token']) || !isset($data['_Token']['fields'])) {
			return false;
		}

		$locked = null;
		$check = $controller->request->data;
		$token = urldecode($check['_Token']['fields']);

		if (strpos($token, ':')) {
			list($token, $locked) = explode(':', $token, 2);
		}
		unset($check['_Token']);

		$locked = explode('|', $locked);

		$lockedFields = array();
		$fields = Set::flatten($check);
		$fieldList = array_keys($fields);
		$multi = array();

		foreach ($fieldList as $i => $key) {
			if (preg_match('/\.\d+$/', $key)) {
				$multi[$i] = preg_replace('/\.\d+$/', '', $key);
				unset($fieldList[$i]);
			}
		}
		if (!empty($multi)) {
			$fieldList += array_unique($multi);
		}

		foreach ($fieldList as $i => $key) {
			$isDisabled = false;
			$isLocked = (is_array($locked) && in_array($key, $locked));

			if (!empty($this->disabledFields)) {
				foreach ((array)$this->disabledFields as $disabled) {
					$disabled = explode('.', $disabled);
					$field = array_values(array_intersect(explode('.', $key), $disabled));
					$isDisabled = ($field === $disabled);
					if ($isDisabled) {
						break;
					}
				}
			}

			if ($isDisabled || $isLocked) {
				unset($fieldList[$i]);
				if ($isLocked) {
					$lockedFields[$key] = $fields[$key];
				}
			}
		}
		sort($fieldList, SORT_STRING);
		ksort($lockedFields, SORT_STRING);

		$fieldList += $lockedFields;
		//pr($fieldList);
		$check = Security::hash(serialize($fieldList) . Configure::read('Security.salt'));
		return ($token === $check);
	}

/**
 * Add authentication key for new form posts
 *
 * @param object $controller Instantiating controller
 * @return bool Success
 */
	protected function _generateToken($controller) {
		if (isset($controller->request->params['requested']) && $controller->request->params['requested'] === 1) {
			if ($this->Session->check('_Token')) {
				$tokenData = $this->Session->read('_Token');
				$controller->request->params['_Token'] = $tokenData;
			}
			return false;
		}
		$authKey = Security::generateAuthKey();
		$token = array(
			'key' => $authKey,
			'allowedControllers' => $this->allowedControllers,
			'allowedActions' => $this->allowedActions,
			'disabledFields' => $this->disabledFields,
			'csrfTokens' => array()
		);

		$tokenData = array();
		if ($this->Session->check('_Token')) {
			$tokenData = $this->Session->read('_Token');
			if (!empty($tokenData['csrfTokens'])) {
				$token['csrfTokens'] = $this->_expireTokens($tokenData['csrfTokens']);
			}
		} 
		if ($this->csrfCheck && ($this->csrfUseOnce || empty($tokenData['csrfTokens'])) ) {
			$token['csrfTokens'][$authKey] = strtotime($this->csrfExpires);
		}
		$controller->request->params['_Token'] = $token;
		$this->Session->write('_Token', $token);
		return true;
	}

/**
 * Validate that the controller has a CSRF token in the POST data
 * and that the token is legit/not expired.  If the token is valid
 * it will be removed from the list of valid tokens.
 *
 * @param Controller $controller A controller to check
 * @return boolean Valid csrf token.
 */
	protected function _validateCsrf($controller) {
		$token = $this->Session->read('_Token');
		$requestToken = $controller->request->data('_Token.key');
		if (isset($token['csrfTokens'][$requestToken]) && $token['csrfTokens'][$requestToken] >= time()) {
			if ($this->csrfUseOnce) {
				$this->Session->delete('_Token.csrfTokens.' . $requestToken);
			}
			return true;
		}
		return false;
	}

/**
 * Expire CSRF nonces and remove them from the valid tokens.
 * Uses a simple timeout to expire the tokens.
 *
 * @param array $tokens An array of nonce => expires.
 * @return An array of nonce => expires.
 */
	protected function _expireTokens($tokens) {
		$now = time();
		foreach ($tokens as $nonce => $expires) {
			if ($expires < $now) {
				unset($tokens[$nonce]);
			}
		}
		return $tokens;
	}

/**
 * Sets the default login options for an HTTP-authenticated request
 *
 * @param array $options Default login options
 * @return void
 */
	protected function _setLoginDefaults(&$options) {
		$options = array_merge(array(
			'type' => 'basic',
			'realm' => env('SERVER_NAME'),
			'qop' => 'auth',
			'nonce' => String::uuid()
		), array_filter($options));
		$options = array_merge(array('opaque' => md5($options['realm'])), $options);
	}

/**
 * Calls a controller callback method
 *
 * @param object $controller Controller to run callback on
 * @param string $method Method to execute
 * @param array $params Parameters to send to method
 * @return mixed Controller callback method's response
 */
	protected function _callback($controller, $method, $params = array()) {
		if (is_callable(array($controller, $method))) {
			return call_user_func_array(array(&$controller, $method), empty($params) ? null : $params);
		} else {
			return null;
		}
	}
}
