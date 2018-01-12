<?php

namespace Xtend\Composer;

use Composer\Script\Event;
use Xtend\Util\FileHelper;

final class Installer
{
  /**
   * @var string DOCUMENT_ROOT
   */
  const DOCUMENT_ROOT = 'public/';

  /**
   * @var string FRAMEWORK_DIR
   */
  const FRAMEWORK_DIR = 'vendor/codeigniter/framework/';

  /**
   * Composer run script
   *
   * @param Event $event
   */
  public static function run(Event $event)
  {
    $cwd = getcwd();
    $io = $event->getIO();

    $io->write('Preparing the application file.');
    FileHelper::copyDirectory(static::FRAMEWORK_DIR . 'application', 'application');
    FileHelper::copyDirectory('src/application', 'application');
    FileHelper::copyDirectory('src/public', DOCUMENT_ROOT);

    $io->write('Create an entry point.');
    FileHelper::copyFile(static::FRAMEWORK_DIR . 'index.php', static::DOCUMENT_ROOT . 'index.php');
    FileHelper::copyFile(static::FRAMEWORK_DIR . '.gitignore', '.gitignore');
    FileHelper::replace(static::DOCUMENT_ROOT . 'index.php', [
      '$system_path = \'system\';' => '$system_path = \'../' . static::FRAMEWORK_DIR . 'system\';',
      '$application_folder = \'application\';' => '$application_folder = \'../application\';',
    ]);

    $io->write('Create a config.');
    FileHelper::replace('application/config/autoload.php', [
      '$autoload[\'libraries\'] = array();' => '$autoload[\'libraries\'] = array(\'session\',\'form_validation\');',
      '$autoload[\'helper\'] = array();' => '$autoload[\'helper\'] = array(\'url\');'
    ]);
    FileHelper::replace('application/config/config.php', [
      '$config[\'base_url\'] = \'\';' => 'if (!empty($_SERVER[\'HTTP_HOST\'])) {$config[\'base_url\'] = "//".$_SERVER[\'HTTP_HOST\'] . str_replace(basename($_SERVER[\'SCRIPT_NAME\']),"",$_SERVER[\'SCRIPT_NAME\']);}',
      '$config[\'index_page\'] = \'index.php\';' => '$config[\'index_page\'] = \'\';',
      '$config[\'enable_hooks\'] = FALSE;' => '$config[\'enable_hooks\'] = TRUE;',
      '$config[\'subclass_prefix\'] = \'MY_\';' => '$config[\'subclass_prefix\'] = \'App\';',
      '$config[\'composer_autoload\'] = FALSE;' => '$config[\'composer_autoload\'] = realpath(\APPPATH . \'../vendor/autoload.php\');',
      '$config[\'permitted_uri_chars\'] = \'a-z 0-9~%.:_\-\';' => '$config[\'permitted_uri_chars\'] = \'a-z 0-9~%.:_\-,\';',
      '$config[\'log_threshold\'] = 0;' => '$config[\'log_threshold\'] = 2;',
      '$config[\'encryption_key\'] = \'\';' => '$config[\'encryption_key\'] = \'b8qmK-/BN,fB{Ce\';',
      '$config[\'sess_driver\'] = \'files\';' => '$config[\'sess_driver\'] = \'database\';',
      '$config[\'sess_save_path\'] = NULL;' => '$config[\'sess_save_path\'] = \'session\';',
      '$config[\'cookie_httponly\']  = FALSE;' => '$config[\'cookie_httponly\']  = TRUE;',
    ]);

    $io->write('Updating composer.');
    FileHelper::copyFile('composer.json.dist', 'composer.json');
    passthru('composer update');

    $io->write('Preparing the frontend module.');
    FileHelper::copyDirectory('src/client', 'client');
    chdir('./client');
    passthru('npm install');
    passthru('npm run build');

    chdir($cwd);
    $io->write('Deleting unnecessary files.');
    FileHelper::delete(
      $cwd . '/src',
      $cwd . '/composer.json.dist',
      $cwd . '/CHANGELOG.md',
      $cwd . '/README.md',
      $cwd . '/LICENSE'
    );

    $io->write('Installation is complete.');
    $io->write('See <https://packagist.org/packages/buddywinangun/codeigniter-xtend> for details.');
  }
}
