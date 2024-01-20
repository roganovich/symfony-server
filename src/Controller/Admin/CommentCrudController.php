<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use Carbon\Carbon;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CommentCrudController extends AbstractCrudController
{

    private $photoDir = '';
    private $baseDir = '';

    public function __construct(#[Autowire('%photo_dir%')] string $photoDir, #[Autowire('%base_dir%')] string $base_dir)
    {
        $this->photoDir = $photoDir;
        $this->baseDir = $base_dir;
    }

    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Conference Comment')
            ->setEntityLabelInPlural('Conference Comments')
            ->setSearchFields(['author', 'text', 'email'])
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('conference'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('conference');
        yield TextField::new('author');
        yield EmailField::new('email');
        yield TextareaField::new('text')->hideOnIndex();

        $filesystem = new Filesystem();
        $basePath = $filesystem->makePathRelative($this->photoDir, $this->baseDir);
        $subDir = Carbon::today()->format('Ymd');

        try {
            $filesystem->mkdir($this->baseDir . DIRECTORY_SEPARATOR . $basePath . $subDir);
        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while creating your directory at " . $exception->getPath();
        }


        yield ImageField::new('photoFilename')
            ->setUploadDir('public' . DIRECTORY_SEPARATOR . $basePath . $subDir)
            ->setBasePath($basePath)
            ->setUploadedFileNamePattern(
                fn (UploadedFile $file): string => $subDir . DIRECTORY_SEPARATOR. bin2hex(random_bytes(6)) . '.' . $file->guessExtension()
            )
            ->setLabel('Photo');

        if (Crud::PAGE_EDIT === $pageName) {
            yield TextField::new('createdAt')->setFormTypeOption('disabled', true);
        } else {
            yield TextField::new('createdAt')->onlyOnIndex();
        }
    }

}
