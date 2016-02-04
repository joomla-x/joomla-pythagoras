<?php
/**
 * Part of the Joomla CMS Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Page\Joomla3\Installer;

use Facebook\WebDriver\WebDriverBy;
use Joomla\Tests\Page\Page;

class InstallerPage extends Page
{
	/** @var  string */
	protected $url = '/installation/index.php';

	/** @var array */
	private $languageNames = [
		'af-ZA'  => 'Afrikaans (South Africa)',
		'ar-AA'  => 'Arabic Unitag (العربية الموحدة)',
		'hy-AM'  => 'Armenian (hy-AM)',
		'id-ID'  => 'Bahasa Indonesia',
		'ms-MY'  => 'Bahasa Melayu (Malaysia)',
		'be-BY'  => 'Belarusian (Беларуская)',
		'bs-BA'  => 'Bosanski (Bosnia)',
		'bg-BG'  => 'Bulgarian (Български)',
		'ca-ES'  => 'Catalan (ES)',
		'zh-CN'  => 'Chinese Simplified 简体中文',
		'zh-TW'  => 'Chinese Traditional (繁體中文 台灣)',
		'cs-CZ'  => 'Czech (Čeština)',
		'da-DK'  => 'Danish (DK)',
		'prs-AF' => 'Dari Persian (فارسی دری)',
		'nl-NL'  => 'Dutch nl-NL',
		'en-AU'  => 'English (Australia)',
		'en-CA'  => 'English (Canada)',
		'en-GB'  => 'English (United Kingdom)',
		'en-US'  => 'English (United States)',
		'et-EE'  => 'Estonian',
		'fi-FI'  => 'Finnish (Suomi)',
		'fr-CA'  => 'Français (Canada)',
		'fr-FR'  => 'Français (Fr)',
		'gl-ES'  => 'Galician (Galiza)',
		'de-DE'  => 'German (DE-CH-AT)',
		'he-IL'  => 'Hebrew (Israel)',
		'hi-IN'  => 'Hindi-हिंदी (India)',
		'hr-HR'  => 'Hrvatski (Croatian)',
		'hu-HU'  => 'Hungarian (Magyar)',
		'it-IT'  => 'Italian (Italy)',
		'ja-JP'  => 'Japanese 日本語 (Japan)',
		'km-KH'  => 'Khmer ភាសាខ្មែរ (Cambodia)',
		'ko-KR'  => 'Korean (Republic of Korea)',
		'ckb-IQ' => 'Kurdish Soranî (کوردى)',
		'lv-LV'  => 'Latvian (LV)',
		'mk-MK'  => 'Macedonian Македонски (MК)',
		'srp-ME' => 'Montenegrin (Latin)',
		'nb-NO'  => 'Norsk bokmål (nb-NO)',
		'nn-NO'  => 'Nynorsk (nn-NO)',
		'fa-IR'  => 'Persian (پارسی)',
		'pl-PL'  => 'Polski (PL)',
		'pt-PT'  => 'Português (pt-PT)',
		'pt-BR'  => 'Português Brasileiro (pt-BR)',
		'ro-RO'  => 'Română (România)',
		'ru-RU'  => 'Russian Русский',
		'sr-RS'  => 'Serbian (Cyrillic)',
		'sr-YU'  => 'Serbian (Latin)',
		'si-LK'  => 'Sinhala (Si)',
		'sk-SK'  => 'Slovak (Slovenčina)',
		'es-ES'  => 'Spanish (Español)',
		'sv-SE'  => 'Svenska (SE)',
		'sw-KE'  => 'Swahili (KE-TZ-UG-RW-BI)',
		'sy-IQ'  => 'Syriac (Iraq)',
		'ta-IN'  => 'Tamil-தமிழ் (India)',
		'th-TH'  => 'Thai ไทย (ภาษาไทย)',
		'tr-TR'  => 'Türkçe (Türkiye)',
		'uk-UA'  => 'Ukrainian-Українська (Україна)',
		'ug-CN'  => 'Uyghur (ئۇيغۇرچە)',
		'vi-VN'  => 'Vietnamese (Vietnam)',
		'cy-GB'  => 'Welsh (United Kingdom)',
		'el-GR'  => 'Ελληνικά',
	];

	protected $elements = [
		'headline'        => ['xpath' => '//h3'],
		'site_name'       => ['id' => 'jform_site_name'],
		'site_metadesc'   => ['id' => 'jform_site_metadesc'],
		'admin_email'     => ['id' => 'jform_admin_email'],
		'admin_user'      => ['id' => 'jform_admin_user'],
		'admin_password'  => ['id' => 'jform_admin_password'],
		'admin_password2' => ['id' => 'jform_admin_password2'],
		'db_host'         => ['id' => 'jform_db_host'],
		'db_user'         => ['id' => 'jform_db_user'],
		'db_pass'         => ['id' => 'jform_db_pass'],
		'db_name'         => ['id' => 'jform_db_name'],
		'db_prefix'       => ['id' => 'jform_db_prefix'],
		'next'            => ['link' => 'Next'],
		'install'         => ['link' => 'Install'],
	];

	/**
	 * @param string $language Language code (recommended) or language name
	 */
	public function setLanguage($language)
	{
		$this->browser->selectOptionInChosen('Select Language', $this->marshalLanguageName($language));
	}

	public function getLanguage()
	{
		$chosenSelectID = $this->findField('Select Language')->getAttribute('id') . '_chzn';

		return $this->marshalLanguageCode(
			trim($this->findElement(WebDriverBy::xpath("//div[@id='$chosenSelectID']/a/span"))->getText())
		);
	}

	public function setSite_offline($value)
	{
		$offset = $value ? 1 : 2;
		$this->findElement(['xpath' => "//fieldset[@id='jform_site_offline']/label[$offset]"])->click();
	}

	public function getSite_offline()
	{
		return $this->findElement(['css' => "#jform_site_offline0"])->isSelected();
	}

	public function setDb_type($value)
	{
		$this->browser->selectOption($this->marshalSelector(['id' => 'jform_db_type']), $value);
	}

	public function getDb_type()
	{
		$this->findElement($this->marshalSelector(['id' => 'jform_db_type']))->getText();
	}

	/**
	 * Installs Joomla
	 */
	public function install($configuration)
	{
		if (file_exists($configuration['Joomla folder'] . '/configuration.php'))
		{
			return;
		}

		$I = $this->browser;

		$I->amOnPage($this->url);

		// Check that FTP tab is not present in installation. Otherwise it means that I have not enough permissions to install joomla and execution will be stopped
		$I->dontSeeElement(['id' => 'ftp']);

		$this->setupSite($configuration);

		$this->browser->waitForText('Database Configuration', 10, $this->elements['headline']);
		$this->set('db_type', $configuration['database type']);
		$this->set('db_host', $configuration['database host']);
		$this->set('db_user', $configuration['database user']);
		$this->set('db_pass', $configuration['database password']);
		$this->set('db_name', $configuration['database name']);
		$this->set('db_prefix', $configuration['database prefix']);
		$I->click(['xpath' => "//label[@for='jform_db_old1']"]); // Remove Old Database button
		$this->click('next');

		$this->debug('I install joomla with or without sample data');
		$this->browser->waitForText('Finalisation', 10, $this->elements['headline']);
		// @todo: installation of sample data needs to be created
		//if ($this->config['install sample data']) :
		//    $this->debug('I install Sample Data:' . $this->config['sample data']);
		//    $I->selectOption('#jform_sample_file', $this->config['sample data']);
		//else :
		//    $this->debug('I install Joomla without Sample Data');
		//    $I->selectOption('#jform_sample_file', '#jform_sample_file0'); // No sample data
		//endif;
		$I->selectOption(['id' => 'jform_sample_file'], ['id' => 'jform_sample_file0']); // No sample data
		$this->click('install');

		// Wait while Joomla gets installed
		$this->debug('I wait for Joomla being installed');
		$this->browser->waitForText('Congratulations! Joomla! is now installed.', 10, $this->elements['headline']);
		$this->debug('Joomla is now installed');
		$I->see('Congratulations! Joomla! is now installed.', $this->elements['headline']);
	}

	/**
	 * @param string $language Language code or language name
	 *
	 * @return string Language name
	 */
	private function marshalLanguageName($language)
	{
		if (isset($this->languageNames[$language]))
		{
			$language = $this->languageNames[$language];
		}

		return $language;
	}

	/**
	 * @param string $language Language code or language name
	 *
	 * @return string Language code
	 */
	private function marshalLanguageCode($language)
	{
		$languageCodes = array_flip($this->languageNames);

		if (isset($languageCodes[$language]))
		{
			$language = $languageCodes[$language];
		}

		return $language;
	}

	/**
	 * @param $configuration
	 */
	protected function setupSite($configuration)
	{
		$this->browser->waitForText('Main Configuration', 10, $this->elements['headline']);
		$this->set('language', $configuration['language']);
		$this->set('site_name', 'Joomla CMS test');
		$this->set('site_metadesc', 'Site for testing Joomla CMS');
		$this->set('admin_email', $configuration['admin email']);
		$this->set('admin_user', $configuration['username']);
		$this->set('admin_password', $configuration['password']);
		$this->set('admin_password2', $configuration['password']);
		$this->set('site_offline', false);
		$this->click('next');
	}
}
