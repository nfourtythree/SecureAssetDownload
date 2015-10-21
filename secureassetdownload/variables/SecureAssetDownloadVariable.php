<?php
namespace Craft;

class SecureAssetDownloadVariable
{

	public function getUrl($criteria)
	{
		return craft()->secureAssetDownload->getUrl($criteria);
	}

}
