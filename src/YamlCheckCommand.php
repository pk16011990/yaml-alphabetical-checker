<?php

namespace YamlAlphabeticalChecker;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Exception\ParseException;

class YamlCheckCommand extends Command
{
    const
        OPTION_DIR = 'dir',
        OPTION_FIX = 'fix';

    protected function configure()
    {
        $this
            ->setName('yaml-alphabetical-check')
            ->setDescription('Check if yaml files is alphabetically sorted')
            ->addOption(self::OPTION_DIR, null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Directories to check')
            ->addOption(self::OPTION_FIX, null, InputOption::VALUE_NONE, 'Fix sort of yaml files');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<fg=green>Start checking yaml files.</fg=green>');
        $output->writeln('');

        $errors = [];
        $yamlAlphabeticalChecker = new YamlAlphabeticalChecker();
        $pathToYamlFiles = YamlFilesPathService::getPathToYamlFiles($input->getOption(self::OPTION_DIR));

        foreach ($pathToYamlFiles as $pathToYamlFile) {
            $output->write(sprintf('Checking %s: ', $pathToYamlFile));

            if (!is_readable($pathToYamlFile)) {
                $output->write(sprintf('<fg=red>File is not readable.</fg=red>'));
                exit(1);
            }

            try {
                $yamlArrayData = $yamlAlphabeticalChecker->parseData($pathToYamlFile);
                $sortCheckResult = $yamlAlphabeticalChecker->isDataSorted($yamlArrayData);

                if ($input->getOption(self::OPTION_FIX) === true) {
                    if (!$sortCheckResult) {
                        $yamlAlphabeticalChecker->sortData($yamlArrayData, $pathToYamlFile);
                    }
                }

                if ($sortCheckResult) {
                    $output->writeln('<fg=green>OK</fg=green>');
                } else {
                    $output->writeln('<fg=red>ERROR</fg=red>');
                    $errors[] = 'e';
                }
            } catch (ParseException $e) {
                $output->writeln(sprintf('Unable to parse the YAML string: %s', $e->getMessage()));
            }
        }

        if (count($errors) > 0) {
            $output->writeln('<fg=red>Tip: Use --fix command to fix errors.</fg=red>');
        }

        $output->writeln('');
        $output->writeln('<fg=green>End of checking yaml files.</fg=green>');
    }
}
