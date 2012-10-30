
/**
 * @param  \Symfony\Component\Form\Form $form
 * @param  PropelPDO $con
 * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
 * @throws PropelException
 * @throws Exception
 * @see    doSave()
 */
public function saveFromCrud(\Silex\Application $app, \Symfony\Component\Form\Form $form, PropelPDO $con = null)
{
    <?php foreach ($columns as $column): ?>if (!$form['<?php echo $column['deletedColumnName'] ?>']->getData()) {
        $this->resetModified(<?php echo $column['fileColumnPeerName'] ?>);
    }

    $this->upload<?php echo $column['filecolumnName'] ?>($form);
    <?php endforeach ?>

    return $this->save($con);
}
