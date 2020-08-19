<?php

namespace App\Command;

use App\Entity\Plantel;
use App\Entity\Escuela;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;

class EscuelasCommand extends Command
{
    protected static $defaultName = 'import:escuelas';

    protected $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('COMANDO QUE IMPORTA LOS DATOS DEL CSV DE ESCUELAS');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $reader = Reader::createFromPath('%kernel.root_dir%/../src/Data/escuelas.csv');
        $header=['cct','nombre'];
        $records = $reader->getRecords($header);
        $i = 0;
        $ccts = "";
        $nombre = "";
        $count=$reader->count();
        $progressBar = new ProgressBar($output, $count);

        foreach ($records as $data) {
            if ($i != 0) {
                //Obteniendo los datos
                $ccts = $data['cct'];
                $nombre = $data['nombre'];

                $plantel = $this->manager->getRepository(Plantel::class)
                                              ->findOneBy(['nombre' => $nombre]);
                if($plantel!=null){
                    $escuela = new Escuela();
                    $escuela->setNombre($nombre);
                    $escuela->setCcts($ccts);
                    $escuela->setPlantel($plantel);
                    $this->manager->persist($escuela);
                }
            }

            $progressBar->advance();
            $i++;
        }
        $this->manager->flush();
        $progressBar->finish();

        return 0;
    }

}
