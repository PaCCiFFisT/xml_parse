<?php

namespace App\Command;

use App\Entity\Offer;
use App\Manager\CrudManager;
use App\Repository\OfferRepository;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use SimpleXMLElement;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:offers:save',
    description: 'Save offers from the external API to the database'
)]
class SaveOffersCommand extends Command
{
    private const API_URL = 'https://prof-market.com.ua/products_feed.xml?hash_tag=1a2b06b34e3305cf6570af7a3376a22c&sales_notes=&product_ids=&label_ids=&exclude_fields=&html_description=0&yandex_cpa=&process_presence_sure=&languages=ru&extra_fields=&group_ids=';
    private const BARCODE_FILE_PATH = '/data/barcodes.xlsx';

    public function __construct(
        private string $projectDir,
        private CrudManager $crudManager,
        private OfferRepository $repository,
        ?string $name = null
    )
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Start saving offers');
        $output->writeln((new  \DateTime('now'))->format('Y-m-d H:i:s'));
        $barcodes = $this->getBarcodes();
        $xml = simplexml_load_file(self::API_URL);
        /** @var SimpleXMLElement[] $offers */
        $offers = $xml->xpath('//offers')[0]->offer;
        $progressBar = new ProgressBar($output, count($offers));
        foreach ($offers as $offerData) {
            if (! $offerData->vendorCode[0] || !isset($barcodes[(string) $offerData->vendorCode[0]])){
                continue;
            }

            $offer = new Offer();
            $this->mapOfferDataToOffer($offer, $offerData, $barcodes);
            $progressBar->advance();

            if ($existOffer = $this->repository->findOneBy(['vendorCode' => $offer->getVendorCode()])) {
                $existOffer->setVendor($offer->getVendor());
                $existOffer->setCategoryId($offer->getCategoryId());
                $existOffer->setVendorCode($offer->getVendorCode());
                $existOffer->setName($offer->getName());
                $existOffer->setPrice($offer->getPrice());
                $existOffer->setBarcode($offer->getBarcode());

                $this->crudManager->update($existOffer);

                continue;
            }

            $this->crudManager->create($offer);
        }

        $output->writeln((new  \DateTime('now'))->format('Y-m-d H:i:s'));

        return Command::SUCCESS;
    }

    private function saveOffers(): void
    {

    }

    private function mapOfferDataToOffer(Offer $offer, SimpleXMLElement $offerData, array $barcodes): void
    {
        $offer->setVendor((string) $offerData->vendor[0] ?? null);
        $offer->setCategoryId((string) $offerData->categoryId[0] ?? null);
        $offer->setVendorCode((string) $offerData->vendorCode[0] ?? null);
        $offer->setName((string) $offerData->name[0]);
        $offer->setPrice(sprintf('%.2f', (float) $offerData->price[0]));
        $offer->setBarcode($barcodes[(string) $offerData->vendorCode[0]] ?? null);
    }

    private function getBarcodes(): array
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load($this->projectDir . self::BARCODE_FILE_PATH);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        $barcodes = [];
        foreach ($rows as $row) {
            $barcodes[$row[0]] = $row[1];
        }

        return $barcodes;
    }
}