<?php
namespace AppBundle\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;

class AdminController extends BaseAdminController
{
    public function preUpdateEntity($entity)
    {
        if (method_exists($entity, 'setDateUpd')) {
            $entity->setDateUpd(new \DateTime());
        }
    }

    protected function prePersistEntity($entity)
    {
      if (method_exists($entity, 'setDateAdd')) {
          $entity->setDateAdd(new \DateTime());
      }
      if (method_exists($entity, 'setDateUpd')) {
          $entity->setDateUpd(new \DateTime());
      }
    }
}
