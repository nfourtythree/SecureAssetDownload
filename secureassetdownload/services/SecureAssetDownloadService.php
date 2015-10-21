<?php
namespace Craft;

class SecureAssetDownloadService extends BaseApplicationComponent
{

	protected $_asset;
	protected $_currentUser;

	public function getUrl($criteria)
	{
		if (isset($criteria['asset'])) {
			if (!$criteria['asset'] instanceof AssetFileModel) {
				$criteria['asset'] = craft()->assets->getFileById($criteria['asset']);

				if (!$criteria['asset']) {
					return null;
				}
			}

			if ($criteria['asset']->url) {
				$options = [
					'id' => $criteria['asset']->id,
					'url' => $criteria['asset']->url,
					'userId' => (isset($criteria['userId']) ? $criteria['userId'] : null),
				];

				if (isset($criteria['userGroupId'])) {
					craft()->requireEdition(Craft::Pro);
					$options['userGroupId'] = $criteria['userGroupId'];
				}

				$urlParam = $this->encodeUrlParam($options);

				return UrlHelper::getSiteUrl('secureAssetDownload/' . $urlParam);
			}
		}

		return null;
	}

	public function serveAsset(array $options = array())
	{
		if (isset($options['id'])) {
			if (!$this->_asset or $this->_asset->id != $options['id'] or !$this->_asset instanceof AssetFileModel) {
				$this->_asset = craft()->asset->getFileById($options['id']);

				if (!$this->_asset) {
					throw new Exception(Craft::t("Unable to find asset"));
				}
			}

			$client = new \Guzzle\Http\Client();
			$response = $client->get($this->_asset->url)->send();

			if ($response->isSuccessful()) {
				craft()->request->sendFile($this->_asset->url, $response->getBody(), array('forceDownload' => true));
			} else {
				throw new Exception(Craft::t("Unable to serve file"));
			}
		}
	}

	public function isDownloadAllowed(array $options = array())
	{
		if (isset($options['id'])) {
			$this->_asset = craft()->assets->getFileById($options['id']);

			if (!$this->_asset) {
				return false;
			}

			if (!craft()->userSession->isLoggedIn()) {
				return false;
			}

			// User related checks
			$this->_currentUser = craft()->userSession->getUser();

			if (!$this->_currentUser) {
				return false;
			}

			if ($this->_currentUser->admin) {
				return true;
			}

			if (isset($options['userId']) and $options['userId']) {
				if (!$this->_checkInArray($this->_currentUser->id, $options['userId'])) {
					return false;
				}
			}

			if (isset($options['userGroupId']) and $options['userGroupId']) {
				$usersGroupIds = array_keys($this->_currentUser->getGroups('id'));

				if (!$usersGroupIds) {
					return false;
				}

				$_returnGroupIdCheck = true;
				foreach ($usersGroupIds as $_groupId) {
					if (!$this->_checkInArray($_groupId, $options['userGroupId'])) {
						$_returnGroupIdCheck = false;
					}
				}

				if (!$_returnGroupIdCheck) {
					return false;
				}

			}

			return true;
		}

		return false;
	}

	private function _checkInArray($needle, $haystack)
	{
		if (!is_array($haystack)) {
			$haystack = array($haystack);
		}

		if (!in_array($needle, $haystack)) {
			return false;
		}

		return true;
	}

	public function encodeUrlParam($options = array())
	{
		$optionsString = serialize($options);

		$url = $this->encrypt($optionsString);

		return $url;
	}

	public function decodeUrlParam($optionsString = "")
	{
		$optionsString = $this->decrypt($optionsString);

		$options = unserialize($optionsString);

		return $options;
	}

	protected function encrypt($string)
	{
		$key = craft()->plugins->getPlugin("secureAssetDownload")->getSettings()->encryptionKey;
	    return rtrim(strtr(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key)))), '+/', '-_'), '=');
	}

	protected function decrypt($string)
	{
		$key = craft()->plugins->getPlugin("secureAssetDownload")->getSettings()->encryptionKey;
	    return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode(str_pad(strtr($string, '-_', '+/'), strlen($string) % 4, '=', STR_PAD_RIGHT)), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
	}

}