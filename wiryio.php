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
			$extras = "";
			if ( $user && $user->get('isRoot')) {
        $extras .= 'WiryConfig(\'dev\', true); // do not track admin users'; 
			}
			$buffer = JResponse::getBody();
			$code = "
			<!-- Wiry.io Plugin -->
				<script>
				(function(scope, targetEl, tag, baseUrl, accountId) {
						var fn, el, script;
						fn = function WiryConfig(prop, val) {
						scope[fn.name]._options[prop] = val;
						};
						scope[fn.name] = fn;
						fn._options = { accountId: accountId, baseUrl: baseUrl, load: new Date() };
						script = targetEl.createElement(tag);
						script.async = 1;
						script.src = baseUrl + '/static/script/bundle.js';
						el = targetEl.getElementsByTagName(tag)[0];
						(el ? el.parentNode.insertBefore(script, el) : targetEl.head.appendChild(script));
				})(window, document, 'script', 'https://gateway.wiryio.com', '{$this->params->get( 'account_id' )}');
				{$extras}
				</script>
			<!-- / Wiry.io Plugin -->
";
			$buffer = preg_replace( "/<\/body>/", "\n" . $code . "\n</body>", $buffer );
			JResponse::setBody( $buffer );
		}

		return;
	}
}
