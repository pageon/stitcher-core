<?php

namespace Brendt\Stitcher\Command;

use Brendt\Stitcher\Console;
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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GenerateCommand extends Command implements EventSubscriberInterface
{

    const ROUTE = 'route';

    /**
     * @var Stitcher
     */
    private $stitcher;

    /**
     * @var ProgressBar
     */
    private $progress;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var string
     */
    private $publicDir;

    public function __construct(string $publicDir, Stitcher $stitcher, EventDispatcher $eventDispatcher) {
        parent::__construct();

        $this->publicDir = $publicDir;
        $this->stitcher = $stitcher;
        $eventDispatcher->addSubscriber($this);
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
        $this->output = $output;

        $route = $input->getArgument(self::ROUTE);
        $blanket = $this->stitcher->stitch($route);
        $this->stitcher->save($blanket);
        $this->progress->finish();

        if ($route) {
            $output->writeln("\n<fg=green>{$route}</> successfully generated in <fg=green>{$this->publicDir}</>.");
        } else {
            $output->writeln("\nSite successfully generated in <fg=green>{$this->publicDir}</>.");
        }
    }

    public static function getSubscribedEvents() {
        return [
            SiteParser::EVENT_PARSER_INIT => 'onSiteParserInit',
            SiteParser::EVENT_PAGE_PARSING => 'onPageParsing',
            SiteParser::EVENT_PAGE_PARSED => 'onPageParsed',
        ];
    }

    public function onSiteParserInit(Event $event) {
        /** @var Site $site */
        $site = $event->getData()['site'] ?? null;

        if (!$site) {
            return;
        }

        $amount = count($site->getPages());

        $this->progress = new ProgressBar($this->output, $amount);
        $this->progress->setBarWidth(40);
        $this->progress->setFormatDefinition('custom', "\n%current%/%max% [%bar%] %message%\n");
        $this->progress->setFormat('custom');
        $this->progress->setMessage('');
        $this->progress->start();
    }

    public function onPageParsing(Event $event) {
        /** @var Page $page */
        $page = $event->getData()['page'] ?? null;

        if (!$this->progress || !$page) {
            return;
        }

        $this->progress->setMessage($page->getId());
    }

    public function onPageParsed() {
        if (!$this->progress) {
            return;
        }

        $this->progress->advance();
    }
}
