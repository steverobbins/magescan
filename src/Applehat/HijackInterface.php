<?php


namespace Applehat;

use Symfony\Component\Console\Output\NullOutput;

class HijackInterface extends NullOutput
{

  var $output = "";

  public function getOutput() {
    $return = $this->output;
    $this->output = "";
    return $return;
  }

  public function write($message,$newline = false,$type = self::OUTPUT_NORMAL) {

    if (is_array($message)) {
      $message = implode("\n",$message);
    }

    if ($newline) {
      $message = $message."\n";
    }

    $this->output = $this->output.$message;
  }

  public function writeln($message,$type = self::OUTPUT_NORMAL) {
    $this->write($message,1,$type);
  }

}
