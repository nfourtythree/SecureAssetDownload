<?php
namespace Craft;

class SecureAssetDownloadController extends BaseController
{
	public function actionIndex(array $variables = array())
	{
		if ($variables and $variables["crypt"]) {
			$options = craft()->secureAssetDownload->decodeUrlParam($variables["crypt"]);

			if (!craft()->secureAssetDownload->isDownloadAllowed($options)) {
				throw new Exception(Craft::t("You do not have permission to download this file"));
			}

			craft()->secureAssetDownload->serveAsset($options);
		}
	}
}
