<?php

namespace App\Command;

use App\Interfaces\MessageHandler;
use Ratchet\Server\IoServer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'run:websocket-server',
    description: 'Start websockets',
)]
class WebsocketServerCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $server = IoServer::factory(
            new MessageHandler(),
            8080
        );
        $server->run();

        return(0);
    }
}