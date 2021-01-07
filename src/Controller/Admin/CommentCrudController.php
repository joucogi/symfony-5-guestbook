<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CommentCrudController extends AbstractCrudController {
    public static function getEntityFqcn(): string {
        return Comment::class;
    }


    public function configureFields(string $pageName): iterable {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('author'),
            EmailField::new('email'),
            DateTimeField::new('createdAt')->hideOnForm(),
            ImageField::new('photoFilename', 'Photo')->setBasePath('/uploads/photos')->setUploadDir('/public/uploads/photos'),
            TextField::new('state')->setFormTypeOption('disabled', true)->setFormTypeOption('required', false),
            AssociationField::new('conference'),
            TextEditorField::new('text')->hideOnIndex(), // Removing ->hideOnIndex() will display a link to a text modal
        ];
    }
}
