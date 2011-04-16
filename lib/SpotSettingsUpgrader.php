<?php
class SpotSettingsUpgrader {
	private $_settings;

	function __construct($settings) {
		$this->_settings = $settings;
	} # ctor
	
	function update() {
		$this->createServerKeys();
		$this->createPasswordSalt();
		$this->setupNewsgroups();
		$this->updateSettingsVersion();
		$this->createRsaKeys();
	} # update()
	
	/*
	 * Set een setting alleen als hij nog niet bestaat
	 */
	function setIfNot($name, $value) {
		if ($this->_settings->exists($name)) {
			return ;
		} # if
		
		$this->_settings->set($name,$value);
	} # setIfNot
	 
	/*
	 * Update de huidige versie van de settings
	 */
	function updateSettingsVersion() {
		$this->_settings->set('settingsversion', SPOTWEB_SETTINGS_VERSION);
	} # updateSettingsVersion
	
	/*
	 * Creeer de server private en public keys
	 */
	function createServerKeys() {
		$spotSigning = new SpotSigning(true);
		$x = $spotSigning->createPrivateKey();
		
		$this->setIfNot('publickey', $x['public']);
		$this->setIfNot('privatekey', $x['private']);
	} # createServerKeys

	/*
	 * Creeer de RSA keys
	 */
	function createRsaKeys() {
		#
		# RSA keys
		# Worden gebruikt om te valideren of spots geldig zijn, hoef je normaal niet aan te komen
		#
		$rsaKeys = array();
		$rsaKeys[2] = array('modulo' => 'ys8WSlqonQMWT8ubG0tAA2Q07P36E+CJmb875wSR1XH7IFhEi0CCwlUzNqBFhC+P',
							'exponent' => 'AQAB');
		$rsaKeys[3] = array('modulo' => 'uiyChPV23eguLAJNttC/o0nAsxXgdjtvUvidV2JL+hjNzc4Tc/PPo2JdYvsqUsat',
							'exponent' => 'AQAB');
		$rsaKeys[4] = array('modulo' => '1k6RNDVD6yBYWR6kHmwzmSud7JkNV4SMigBrs+jFgOK5Ldzwl17mKXJhl+su/GR9',
							'exponent' => 'AQAB');
		
		$this->setIfNot('rsa_keys', $rsaKeys);
	} # createRsaKeys
	/*
	 * Creer de servers' password salt
	 */
	function createPasswordSalt() {
		$userSystem = new SpotUserSystem(null, null);
		$salt = $userSystem->generateSessionId() . $userSystem->generateSessionId();
		
		$this->setIfNot('pass_salt', $salt);
	} # createPasswordSalt

	/*
	 * Definieer de standaard SpotNet groepen
	 */
	function setupNewsgroups() {
		$this->setIfNot('hdr_group', 'free.pt');
		$this->setIfNot('nzb_group', 'alt.binaries.ftd');
		$this->setIfNot('comment_group', 'free.usenet');
	} # setupNewsgroups()
	
} # SpotSettingsUpgrader