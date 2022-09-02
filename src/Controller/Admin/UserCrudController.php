<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Utils\Pesel;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {

        if ($pageName == 'new') {
            return [
                TextField::new('name'),
                TextField::new('surname'),
                TextField::new('email'),
                TextField::new('pesel'),
                TextField::new('source')->setValue('UI')->setDisabled(),
            ];
        }

        return [
            IdField::new('id'),
            TextField::new('name'),
            TextField::new('surname'),
            TextField::new('email'),
            TextField::new('source'),
            BooleanField::new('activated'),
            DateField::new('createdAt'),
            IntegerField::new('age'),
            TextField::new('missingAdolescenceText')->setLabel('Adolescence')
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        // TODO filters that filter users registered in 7, 14 and 30 last days
        return $filters;
    }

    // TODO User create form validation
}
