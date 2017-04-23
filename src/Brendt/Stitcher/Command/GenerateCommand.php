<?php

namespace Brendt\Stitcher\Command;

use AsyncInterop\Loop;
use Brendt\Stitcher\Event\Event;
use Brendt\Stitcher\Site\Page;
use Brendt\Stitcher\Site\Site;
use Brendt\Stitcher\SiteParser;
use Brendt\Stitcher\Stitcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class GenerateCommand extends Command
{

    const ROUTE = 'route';

    /**
     * @var Stitcher
     */
    private $stitcher;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var ProgressBar
     */
    private $progress;

    public function __construct(string $configPath = './config.yml', array $defaultConfig = []) {
        parent::__construct();

        $this->stitcher = Stitcher::create($configPath, $defaultConfig);
    }

    protected function configure() {
        $this->setName('site:generate')
            ->setDescription('Generate the website')
            ->setHelp("This command generates the website based on the data in the src/ folder.")
            ->addArgument(self::ROUTE, null, 'Specify a route to render');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $route = $input->getArgument(self::ROUTE);

        $this->eventDispatcher = Stitcher::get('service.event.dispatcher');
        $this->eventDispatcher->addListener(SiteParser::EVENT_PARSER_INIT, function (Event $event) use ($output) {
            /** @var Site $site */
            $site = $event->getData()['site'] ?? null;

            if (!$site) {
                return;
            }

            $amount = count($site->getPages());

            $this->progress = new ProgressBar($output, $amount);
            $this->progress->setFormatDefinition('custom', "%current%/%max% -- %message%\n[%bar%]");
            $this->progress->setFormat('custom');
            $this->progress->start();
        });

        $this->eventDispatcher->addListener(SiteParser::EVENT_PAGE_PARSING, function (Event $event) use ($output) {
            if (!$this->progress) {
                return;
            }

            /** @var Page $page */
            $page = $event->getData()['page'] ?? null;

            if (!$page) {
                return;
            }

            $this->progress->setMessage($page->getId());
        });


        $this->eventDispatcher->addListener(SiteParser::EVENT_PAGE_PARSED, function (Event $event) use ($output) {
            if (!$this->progress) {
                return;
            }

            $this->progress->advance();
        });

        $blanket = $this->stitcher->stitch($route);
        $this->stitcher->save($blanket);
        $this->progress->setMessage('');
        $this->progress->finish();

        $publicDir = Stitcher::getParameter('directories.public');

        if ($route) {
            $output->writeln("\n\n<fg=green>{$route}</> successfully generated in <fg=green>{$publicDir}</>.");
        } else {
            $output->writeln("\n\nSite successfully generated in <fg=green>{$publicDir}</>.");
        }
    }

}
