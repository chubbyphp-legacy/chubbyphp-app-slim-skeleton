<?php

namespace SlimSkeleton\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class RunSqlCommand
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    public function __invoke(InputInterface $input, OutputInterface $output)
    {
        if (($sql = $input->getArgument('sql')) === null) {
            throw new \RuntimeException("Argument 'SQL' is required in order to execute this command correctly.");
        }

        $depth = $input->getOption('depth');

        if (!is_numeric($depth)) {
            throw new \LogicException("Option 'depth' must contains an integer value");
        }

        if (stripos($sql, 'select') === 0) {
            $resultSet = $this->connection->fetchAll($sql);
        } else {
            $resultSet = $this->connection->executeUpdate($sql);
        }

        ob_start();

        \Doctrine\Common\Util\Debug::dump($resultSet, (int) $depth);

        $message = ob_get_clean();

        $output->write($message);
    }
}
