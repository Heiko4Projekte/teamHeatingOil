<?php

namespace Slashworks\SwCalc\Helper;

use Contao\File;
use Contao\FrontendTemplate;

class Shopvote
{

    /**
     * @var string The URL for the API request.
     */
    protected $sApiUrl = 'https://api.shopvote.de/ratings/json/10343/e0851693342b24544bc03715a3f70ac6';

    /**
     * @var \stdClass An object holding all shopvote data.
     */
    protected $oData;

    /**
     * @var string Complete path to the cache file.
     */
    protected $sCacheFilePath = 'system/modules/swCalc/assets/shopvoteData.txt';

    /**
     * @var File The cache file that holds shopvote data.
     */
    protected $oCacheFile;

    /**
     * @var int The cache duration after which new shopvote dat should be get via their API.
     */
    protected $iCacheDuration = 60 * 60 * 24; // = 1 day

    /**
     * Shopvote constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->oCacheFile = new File($this->sCacheFilePath);

        // Update shopvoting data if file is empty
        if ($this->oCacheFile->size === 0) {
            $this->updateShopvoteData();
        }

        $this->checkCacheExpiration();
        $this->getShopvoteData();
    }

    /**
     * Get shopvote data from cache file.
     *
     * @return mixed
     */
    protected function getShopvoteData()
    {
        $this->oData = json_decode($this->oCacheFile->getContent());
    }

    /**
     * Check if the cached data is still valid, otherwise update it per API call.
     */
    protected function checkCacheExpiration()
    {
        $iNow = time();

        $iLastModified = $this->oCacheFile->mtime;

        if ($iNow - $iLastModified > $this->iCacheDuration) {
            $this->updateShopvoteData();
        }
    }

    /**
     * Update data in cache file with data from API.
     */
    protected function updateShopvoteData()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, 'SVApiV1; L25ext for ShopID ' . $svShopID );
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $this->sApiUrl);
        $output = curl_exec($ch);
        curl_close($ch);

        $this->oCacheFile->write($output);
    }

    /**
     * Generate summary element.
     *
     * @return string
     */
    public function generateSummary()
    {
        $iStarCount = 5; // Amount of stars
        $iValue = $this->oData->rating_summary->AllVotes->value; // Percentage of positive ratings, e. g. 90
        $fRating = round($iStarCount / 100 * $iValue, 2); // Rating in comparison to star count, e. g. 4.85

        $oTemplate = new FrontendTemplate('shopvote_summary');
        $oTemplate->ratingPercentage = $iValue;
        $oTemplate->rating = number_format($fRating, 1, ',', '.');
        $oTemplate->starCount = $iStarCount;
        $oTemplate->counter = $this->oData->rating_summary->AllVotes->counter;

        return $oTemplate->parse();
    }

    /**
     * Generated rich snippet data from shopvote in ld+json format.
     *
     * @return string
     */
    public function generateRichSnippetData()
    {
        $iStarCount = 5; // Amount of stars
        $iValue = $this->oData->rating_summary->AllVotes->value; // Percentage of positive ratings, e. g. 90
        $fRating = round($iStarCount / 100 * $iValue, 2); // Rating in comparison to star count, e. g. 4.85

        $oTemplate = new FrontendTemplate('shopvote_rich_snippet');
        $oTemplate->rating = $fRating;
        $oTemplate->counter = $this->oData->rating_summary->AllVotes->counter;
        $oTemplate->profile = $this->oData->profile;

        return $oTemplate->parse();
    }

}