<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;
class Parser extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:parse';

    protected function configure()
    {
        $this
            // configure an argument
            ->addArgument('file', InputArgument::REQUIRED, 'The file of be parsed.')

            // the short description shown while running "php bin/console list"
            ->setDescription('Parses a file.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to parse either a .json file or a gemfile.lock for dependencies')
        ;    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        // retrieve the argument value using getArgument()
        $file = $input->getArgument('file');

        $segments = explode(".",$file);
        $ending = end($segments);

        //determines the parser to be used and passes the file to said command
        if($ending == 'json'){
            $parsertype ='app:json';
        }
        elseif ($ending == 'lock'){
            $parsertype = 'app:lock';
        }
        else{
            $output->writeln('Invalid file format only .json or .lock files are accepted');
            return 0;
        }
        $command = $this->getApplication()->find($parsertype);

        $arguments = [
            'file' => $file
        ];

        $inputs = new ArrayInput($arguments);
        $command->run($inputs, $output);

        return 0;
    }
}