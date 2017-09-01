<?php

namespace Brendt\Stitcher\Command;

use Brendt\Stitcher\Event\Event;
use Brendt\Stitcher\Lib\Browser;
use Brendt\Stitcher\Parser\Site\SiteParser;
use Brendt\Stitcher\Stitcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GenerateCommand extends Command implements EventSubscriberInterface
{
    const ROUTE = 'route';

    private $browser;
    private $stitcher;
    private $output;

    public function __construct(Browser $browser, Stitcher $stitcher, EventDispatcher $eventDispatcher)
    {
        parent::__construct();

        $this->browser = $browser;
        $this->stitcher = $stitcher;
        $eventDispatcher->addSubscriber($this);
    }

    protected function configure()
    {
        $this->setName('site:generate')
            ->setDescription('Generate the website')
            ->setHelp("This command generates the website based on the data in the src/ folder.")
            ->addArgument(self::ROUTE, null, 'Specify a route to render');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $route = $input->getArgument(self::ROUTE);

        $startTime = microtime(true);
        $this->stitcher->stitch($route);

        if (!$route) {
            $this->saveGeneralFiles();
        }

        $endTime = microtime(true);
        $processTime = round($endTime - $startTime, 3);

        if ($route) {
            $output->writeln("\n<fg=green>{$route}</> successfully generated in <fg=green>{$this->browser->getPublicDir()}</>. ({$processTime}s)");
        } else {
            $output->writeln("\nSite successfully generated in <fg=green>{$this->browser->getPublicDir()}</>. ({$processTime}s)");
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            SiteParser::EVENT_PAGE_PARSED => 'onPageParsed',
        ];
    }

    public function onPageParsed(Event $event)
    {
        $pageId = $event->getData()['pageId'] ?? null;
        $this->output->writeln("<fg=green>✔</> {$pageId}");
    }

    private function saveGeneralFiles()
    {
        $this->stitcher->saveHtaccess();
        $this->output->writeln("\n<fg=green>✔</> .htaccess");

        if ($this->stitcher->getSiteMap()->isEnabled()) {
            $this->stitcher->saveSitemap();
            $this->output->writeln("<fg=green>✔</> sitemap.xml");
        }
    }
}
