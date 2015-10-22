# Secure Asset Download
#### Version 1.0.0

Secure Asset Download is a simple Craft CMS plugin allowing you, from your templates, to generate download URLs for specified assets that are only available to certain users/groups

## Installing

1. Copy the `secureassetdownload` directory into your `craft/plugins` directory
2. Navigate to Settings > Plugins in the Craft CP
3. Click on the Install button next to Secure Asset Download
4. Set secret key in plugin settings by clicking Secure Asset Download
4. Use `{{ craft.secureAssetDownload.getUrl(options) }}` to generate secure URL (see usage below)

## Settings

**Encryption Key:** Secret key used when encrypting the URLs

## Options

*asset* is the only required option. If no other options are set any logged in user will be able to access generated URLs.
Admins have access to all generated URLs regardless of options

```
{
	asset: id or AssetFileModel, // e.g 14 or craft.asset.id(23).first()
	userId: id or array of user ids, // e.g. 6 or [ 17, 28, 30 ]
	userGroupId: id or array of user group ids, // e.g. 3 or [ 1, 99, 76 ]
}
```

_note_: Options are *AND* logic

## Usage

#### Secure for all logged in users ####

```
{% set options = {
	asset: 17
} %}
```

#### Secure for any users in specifc group  ####

```
{% set options = {
	asset: 17,
	userGroupId: 2
} %}
```

#### Secure for any users any specified group  ####

```
{% set options = {
	asset: 17,
	userGroupId: [ 2, 6 ]
} %}
```

#### Secure for specific users  ####

```
{% set options = {
	asset: 17,
	userId: [ 46, 86 ]
} %}
```

**Followed by**
```
{% set url = craft.secureAssetDownload.getUrl(options) %}

<a href="{{ url }}">Download me</a><br><br>
```

## Changelog

* 1.0
	* Initial release