<?php

namespace Xtend\Util;

/**
 * File helper class
 *
 * @author     Buddy Winangun
 * @license    MIT License
 * @copyright  2016 Buddy Winangun
 */

final class FileHelper
{

  /**
   *
   * Make directory
   *
   * @throws RuntimeException
   * @param string $dirPath
   * @param int $mode
   * @return void
   */
  public static function makeDirecoty(string $dirPath, int $mode = 0755)
  {
    if (file_exists($dirPath)) {
      return;
    }
    if (mkdir($dirPath, $mode, true) === false) {
      throw new \RuntimeException('Cant create directory ' . $dirPath);
    }
  }

  /**
   *
   * Rename
   *
   * @throws RuntimeException
   * @param string $srcFilePath
   * @param string $dstFilePath
   * @return void
   */
  public static function rename(string $srcFilePath, string $dstFilePath)
  {
    if (!file_exists($srcFilePath)) {
      throw new \RuntimeException('Not found file ' . $srcFilePath);
    }
    self::makeDirecoty(dirname($dstFilePath));
    if (rename($srcFilePath, $dstFilePath) === false) {
      throw new \RuntimeException('Can not rename from ' . $srcFilePath . ' to ' . $dstFilePath);
    }
  }

  /**
   *
   * Copy file
   *
   * @throws RuntimeException
   * @param string $srcFilePath
   * @param string $dstFilePath
   * @return void
   */
  public static function copyFile(string $srcFilePath, string $dstFilePath)
  {
    if (!file_exists($srcFilePath)) {
      throw new \RuntimeException('Not found file ' . $srcFilePath);
    } else if (!is_file($srcFilePath)) {
      throw new \RuntimeException($srcFilePath . ' is not file');
    }
    self::makeDirecoty(dirname($dstFilePath));
    if (copy($srcFilePath, $dstFilePath) === false) {
      throw new \RuntimeException('Can not copy from ' . $srcFilePath . ' to ' . $dstFilePath);
    }
  }

  /**
   *
   * Copy directory
   *
   * @throws RuntimeException
   * @param string $srcDirPath
   * @param string $dstDirPath
   * @return void
   */
  public static function copyDirectory(string $srcDirPath, string $dstDirPath)
  {
    if (!file_exists($srcDirPath)) {
      throw new \RuntimeException('Not found directory ' . $srcDirPath);
    } else if (!is_dir($srcDirPath)) {
      throw new \RuntimeException($srcDirPath . ' is not directory');
    }
    self::makeDirecoty($dstDirPath);
    $iterator = new \RecursiveIteratorIterator(
      new \RecursiveDirectoryIterator($srcDirPath, \RecursiveDirectoryIterator::SKIP_DOTS),
      \RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($iterator as $file) {
      if ($file->isDir()) {
        self::makeDirecoty($dstDirPath . '/' . $iterator->getSubPathName());
      } else {
        self::copyFile($file, $dstDirPath . '/' . $iterator->getSubPathName());
      }
    }
  }

  /**
   *
   * Delete directory or file
   *
   * @param string[] $paths
   * @param bool $isRemoveRootDir
   */
  public static function delete(...$paths)
  // public static function delete(string ...$paths)
  {
    $isRemoveRootDir = true;
    if (is_bool(end($paths))) {
      $isRemoveRootDir = end($paths);
      unset($paths[count($paths) - 1]);
    }
    foreach ($paths as $path) {
      if (is_file($path)) {
        unlink($path);
        continue;
      }
      $iterator = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
        \RecursiveIteratorIterator::CHILD_FIRST
      );
      foreach ($iterator as $file) {
        if ($file->isDir()) {
          rmdir($file);
        } else {
          unlink($file);
        }
      }
      if ($isRemoveRootDir) {
        rmdir($path);
      }
    }
  }

  /**
   *
   * Replace file content
   *
   * @param string $path
   * @return  void
   */
  public static function replace(string $path, array $replace)
  {
    $content = file_get_contents($path);
    $content = str_replace(array_keys($replace), array_values($replace), $content);
    file_put_contents($path, $content);
  }

  /**
   *
   * Find file
   *
   * @param  string $pattern
   * @return array
   */
  public static function find(string $pattern): array
  {
    $files = [];
    foreach (glob($pattern) as $file) {
      $files[] = basename($file);
    }
    return $files;
  }
}
