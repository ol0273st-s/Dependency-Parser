<?php


namespace App\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class JsonParser extends Command
{


    protected static $defaultName = 'app:json';
    protected function configure()
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'The file of be parsed.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        set_error_handler(function ($severity, $message, $file, $line) {
            throw new \ErrorException($message, $severity, $severity, $file, $line);
        });
        $dir = getcwd();
        $file = $input->getArgument('file');
        try {
            $handle = fopen($dir . '/' . $file, 'r');
        }
        catch (\Exception $e){
            $output->writeln("file not found");
            return 0;
        }

        $data = fread($handle, filesize($file));

        $dep = $this->search($data, "dependencies");
        $output->writeln("Dependencies: Version");
        $this->printall($dep);

        $devdep = $this->search($data, "devDependencies");

        $output->writeln("DevDependencies: Version");

        $this->printall($devdep);


        restore_error_handler();

        return 0;

    }

    /**
     * @param $object / object to be searched in
     * @param $search / term searched for
     * @return mixed / values mapped to the search term
     */
    function search($object, $search)
    {
        $decode_data = json_decode($object);
        foreach ($decode_data as $key => $value) {
            if ($key == $search) {
                return ($value);
            }
        }
    }

    /**
     * @param $array / array to be printed
     */
    function printall($array)
    {
        echo ("\n");
        if($array == null){
            echo("NOT FOUND"."\n");
            return;
        }
        foreach ($array as $key => $value) {
            $key = json_encode($key);
            $value = json_encode($value);
            echo("$key : $value");
            echo ("\n");
        }
        echo ("\n");

    }

}