
/**
 * @return string
 */
public function getUploadRootDir()
{
    return __DIR__.'<?php echo $dir ?>' . $this->getUploadDir();
}
