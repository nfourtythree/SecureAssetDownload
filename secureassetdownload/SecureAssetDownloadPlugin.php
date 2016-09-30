<?php
namespace Craft;


class SecureAssetDownloadPlugin extends BasePlugin
{
  function getName()
  {
    return 'Secure Asset Download';
  }

  function getVersion()
  {
    return '1.0.3';
  }

  function getDeveloper()
  {
    return 'nfourtythree';
  }

  function getDeveloperUrl()
  {
    return 'http://n43.me';
  }

  public function getDocumentationUrl()
  {
      return 'https://github.com/nfourtythree/SecureAssetDownload/blob/master/README.md';
  }

  public function getReleaseFeedUrl()
  {
      return 'https://raw.githubusercontent.com/nfourtythree/SecureAssetDownload/master/releases.json';
  }

  public function hasCpSection()
  {
    return false;
  }

  protected function defineSettings()
  {
    return array(
      'encryptionKey' => array(AttributeType::String, 'default' => "My Super Secret Key"),
    );
  }

  public function getSettingsHtml()
  {
    return craft()->templates->render('secureassetdownload/_settings', array(
      'settings' => $this->getSettings()
    ));
  }

  public function registerSiteRoutes()
  {
      return array(
          'secureAssetDownload/(?P<crypt>.+)' => array('action' => 'secureAssetDownload/index'),
      );
  }
}
