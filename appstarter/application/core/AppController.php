<?php

/**
 * This file is part of Codeigniter Xtend.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Core;

abstract class AppController extends \Xtend\Controller
{
  protected function beforeResponse(string $referer) {}
}