<?php
/**
 * HijackInterface
 *
 * PHP version 5
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Cameron Chunn <cameronchunn@gmail.com>
 * @copyright 2015 Simple Helix, LLC
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/simplehelix/magescan
 */


namespace Applehat;


use Symfony\Component\Console\Output\NullOutput;


/**
 * Pickyback NullOutput
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Cameron Chunn <cameronchunn@gmail.com>
 * @copyright 2015 Simple Helix, LLC
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/simplehelix/magescan
 */
class HijackInterface extends NullOutput
{

  private $output = "";

  /**
   * Return and clear output
   *
   * @return string
   */
  public function getOutput() {
    $return = $this->output;
    $this->output = "";
    return $return;
  }

  /**
   * Hijack the write function
   *
   * @param string $message
   * @param bool $newline
   * @param string $type
   *
   * @return void
   */
  public function write($message,$newline = false,$type = self::OUTPUT_NORMAL) {

    if (is_array($message)) {
      $message = implode("\n",$message);
    }

    if ($newline) {
      $message = $message."\n";
    }

    $this->output = $this->output.$message;
  }

  /**
   * Hijack the writeln function
   *
   * @param string $message
   * @param string $type
   *
   * @return void
   */
  public function writeln($message,$type = self::OUTPUT_NORMAL) {
    $this->write($message,1,$type);
  }

}
