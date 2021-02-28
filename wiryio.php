<?php

/**
 * @package		Wiry.io
 * @author		Wiry.io - https://wiry.io
 * @copyright	Copyright (c) 2021 Wiry Solutions OU
 * @license		GNU/GPL license: http://www.gnu.org/licenses/gpl-2.0.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );

class plgSystemWiryio extends JPlugin {

	function onAfterRender() {
		$app = JFactory::getApplication();
		$user = JFactory::getUser();

		if ($app->isAdmin() ) {
			return;
		}

		if ( $this->params->get( 'account_id' ) ) {
			$domain = $this->params->get( 'domain' );
			$version = $this->params->get( 'script_version' );
			$extras = (object) array();
			if ($this->params->get( 'extras' )) {
				$extras = (object) array_merge((array) $extras, (array) json_decode($this->params->get( 'extras' )));
			}
			if ($user && $user->get('isRoot')) {
				$extras->doNotTrack = "strict";
			}
			if (!$domain) {
				$domain = "gateway.wiryio.com";
			}
			if (!$version) {
				$version = "1.0";
			}
			$buffer = JResponse::getBody();
			$json_extras = urlencode(json_encode($extras));
			$code = "
			<!-- Wiry.io Plugin -->
			<script
				async
				src=\"https://{$domain}/script/{$version}/{$this->params->get( 'account_id' )}.js\"
				data-options=\"{$json_extras}\"
			></script>
			<!-- / Wiry.io Plugin -->
";
			$buffer = preg_replace( "/<\/body>/", "\n" . $code . "\n</body>", $buffer );
			JResponse::setBody( $buffer );
		}

		return;
	}
}
