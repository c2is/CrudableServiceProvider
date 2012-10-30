
/**
 * @param \Symfony\Component\Form\Form $form
 * @return void
 */
public function upload<?php echo $columnCamelize ?>(\Symfony\Component\Form\Form $form)
{
    if (!file_exists($this->getUploadRootDir() . '/' . $form['<?php echo $column ?>']->getData()))
    {
        $image = sprintf("%s.%s", uniqid(), $form['<?php echo $column ?>']->getData()->guessExtension());
        $form['<?php echo $column ?>']->getData()->move($this->getUploadRootDir(), $image);
        $this->set<?php echo $columnCamelize ?>(sprintf("%s/%s", $this->getUploadDir(), $image));
    }
}
