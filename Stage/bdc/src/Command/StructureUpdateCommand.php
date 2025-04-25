<?php

namespace App\Command;

use App\Services\Structure;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:structure-update',
    description: 'Mise à jour des structures',
    hidden: false
)]
class StructureUpdateCommand extends Command
{
    public function __construct(private Structure $structure)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $now = new \DateTime();

        $poles = $this->structure->load();
        $add = $this->structure->add($poles);
        $remove = $this->structure->remove($poles);
        $update = $this->structure->update($poles);

        $message = $add['nbPoles'] . " pôles ajouté(s) ; ";
        $message .= $add['nbDirections'] . " directions ajoutée(s) ; ";
        $message .= is_array($remove) ? $remove['nbPoles'] . " pôles supprimé(s) ; " : '';
        $message .= is_array($remove) ? $remove['nbDirections'] . " directions supprimé(s) " : '';
        $message .= is_array($update) ? $update['nbPoles'] . " pôles modifié(s) ; " : '';
        $message .= is_array($update) ? $update['nbDirections'] . " directions modifié(s) " : '';

        $output->writeln($now->format('Y-m-d H:i:s ') . $message);

        return Command::SUCCESS;
    }
}