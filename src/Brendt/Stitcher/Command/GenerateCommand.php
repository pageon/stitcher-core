<?php

namespace Brendt\Stitcher\Command;

use Brendt\Stitcher\Event\Event;
use Brendt\Stitcher\Site\Page;
use Brendt\Stitcher\Site\Site;
use Brendt\Stitcher\Parser\Site\SiteParser;
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

        $startTime = microtime(true);
        $this->stitcher->stitch($route);
        $this->stitcher->saveHtaccess();
        $this->stitcher->saveSitemap();
        $endTime = microtime(true);

        $processTime = round($endTime - $startTime, 3);

        if ($route) {
            $output->writeln("<fg=green>{$route}</> successfully generated in <fg=green>{$this->publicDir}</>. ({$processTime}s)");
        } else {
            $output->writeln("Site successfully generated in <fg=green>{$this->publicDir}</>. ({$processTime}s)");
        }
    }

    public static function getSubscribedEvents() {
        return [
            SiteParser::EVENT_PAGE_PARSED => 'onPageParsed',
        ];
    }

    public function onPageParsed(Event $event) {
        $pageId = $event->getData()['pageId'] ?? null;
        $this->output->writeln("<fg=green>✔</> {$pageId}");
    }
}
