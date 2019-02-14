<?php

namespace Slashworks\SwCalc\Hooks;

use Contao\Input;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;
use Slashworks\SwCalc\Models\Collection;

class GeneratePage
{

    public function addCollectionDataToGlobalData(PageModel $oPage, LayoutModel $oLayout, PageRegular $oPageRegular)
    {
        /** @var Collection $oCollection */
        $oCollection = Collection::getCurrent();

        if ($oCollection !== null) {
            global $objPage;

            if ($objPage->collectionData) {
                $collectionData = $objPage->collectionData;
                $collectionData = array_merge($collectionData, $oCollection->row());
            } else {
                $collectionData = $oCollection->row();
            }

            $objPage->collectionData = $collectionData;
        }

    }

}
