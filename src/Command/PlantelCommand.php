<?php

namespace App\Command;

use App\Entity\Ciudad;
use App\Entity\CodigoPostal;
use App\Entity\Estado;
use App\Entity\Municipio;
use App\Entity\Plantel;
use App\Entity\TipoAsentamiento;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;

class PlantelCommand extends Command
{
    protected static $defaultName = 'import:plantel';

    protected $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('COMANDO QUE IMPORTA LOS DATOS DEL CSV DE LOS PLANTELES');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $reader = Reader::createFromPath('%kernel.root_dir%/../src/Data/plantel.csv');
        $header=['nombre'];
        $records = $reader->getRecords($header);
        $i = 0;
        $nombre = "";
        $count=$reader->count();
        
        $progressBar = new ProgressBar($output, $count);

        foreach ($records as $data) {
            //$io->success("Fila ".($i+1)."/".$count);
            if ($i != 0) {
                //Obteniendo los datos
                $nombre = $data['nombre'];
                if (""!=$nombre) {
                    $plantel = new Plantel();
                    $plantel->setNombre($nombre);
                    $this->manager->persist($plantel);
                }
                $this->manager->flush();
            }
            $i++;
            $progressBar->advance();
        }
        $progressBar->finish();

        return 0;
    }

}
