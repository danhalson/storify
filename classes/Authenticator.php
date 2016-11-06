<?php
	/**
	 * Authenticates a user.
	 *
	 * This is written around Heroku and it's environment variables,
	 * it's probably not particularly useful out of that context, though
	 * it could be extended.
	 */
	class Authenticator {
		protected $sessionCreated = 0;
		protected $sessionIdentifier = '';
		protected $sessionTimeout = 0;

		protected $access = false;
		protected $user = '';
		protected $hash = '';

		/**
		 * Set up the session, and get the valid credentials.
		 *
		 * @return Authenticator $this
		 */
		public function __construct() {
			session_start();

			$this->sessionCreated = time();
			$this->sessionIdentifier = sha1($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
			$this->sessionTimeout = 30 * 60;

			$this->getValidCredentials();

			return $this;
		}

		/**
		 * This is the Heroku oriented bit, get the environment
		 * variables STORIFY_USER + STORIFY_HASH, that will have
		 * been created in advance.
		 *
		 * @return Authenticator $this
		 */
		protected function getValidCredentials() {
			$this->realUser = getenv('STORIFY_USER');
			$this->hash = getenv('STORIFY_HASH');

			return $this;
		}

		/**
		 * Check if we have an existing and valid session.
		 *
		 * @return boolean
		 */
		public function checkSession() {
			if (!empty($_SESSION["access"]) && $_SESSION["access"] === $this->sessionIdentifier) {
				if (!empty($_SESSION['created']) && ($this->sessionCreated + $this->sessionTimeout) > time()) {
					$this->access = true;
					return true;
				} else {
					// Tidy up if the session has expired.
					session_destroy();
				}
			}

			return false;
		}

		/**
		 * Check the passed credentials against he valid ones.
		 *
		 * @return boolean
		 */
		public function checkCredentials() {
			if ($this->user === $this->realUser && password_verify($this->pass, $this->hash)) {
				$_SESSION["access"] = $this->sessionIdentifier;
				$_SESSION['created'] = $this->sessionCreated;
				$this->access = true;
				return true;
			} else {
				throw new Exception('Incorrect credentials');
			}

			return false;
		}

		/**
		 * Sets the user's credentials.
		 *
		 * @param string $user [description]
		 * @param string $pass [description]
		 *
		 * @return Authenticator $this
		 */
		public function setCredentials($user, $pass) {
			$this->user = $user;
			$this->pass = $pass;

			return $this;
		}
	}
?>
