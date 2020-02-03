<?php


namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class GemfileParser extends Command
{
    protected static $defaultName = 'app:lock';
    protected function configure()
    {   $this
            ->addArgument('file', InputArgument::REQUIRED, 'The file of be parsed.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        set_error_handler(function ($severity, $message, $file, $line) {
            throw new \ErrorException($message, $severity, $severity, $file, $line);
        });
        $my_file = $input->getArgument('file');
        try {
            $handle = fopen( $my_file, 'r');
        }
        catch(\Exception $e){
            $output->writeln("file not found");
            return 0;
        }
        $data = fread($handle,filesize($my_file));
        $needle = 'DEPENDENCIES';
        $data = strstr ($data , $needle);
        $data = strstr($data,"\n\n",true);
        $data_list = explode("\n",$data);
        if(count($data_list)==1){
            $output->writeln("NO DEPENDENCIES FOUND");
        }
        foreach ($data_list as $item) {
            $output->writeln($item);
        }
        restore_error_handler();

        return 0;
    }

}