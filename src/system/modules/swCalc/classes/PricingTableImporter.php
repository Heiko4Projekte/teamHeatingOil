<?php


namespace Slashworks\SwCalc\Classes;


use Contao\File;

class PricingTableImporter
{

    protected $mapping = [];

    protected $sTable = "pricing";

    protected $sBackupTable = "pricing_backup";

    protected $sMessage = 'Update erfolgreich - Backup angelegt';


    public function importPricingTable($value, $dc)
    {

        return $value;
    }


    public function updateConfiguration($dc)
    {

        try {

            $this->addMapping();
            $values = [];

            $oConfig = \Slashworks\SwCalc\Models\Configuration::findById($dc->id);
            $file = \FilesModel::findByUuid($oConfig->pricingTable);

            $oFile = new File($file->path,true);

            if (!$oFile->exists()) {
                throw new \Exception('File specified in configuration do not exist');
            }

            while(($arrRow = @fgetcsv($oFile->handle, null, ';')) !== false)
            {
                //check if row has an email addess
                if(strpos($arrRow[1],'@' )){
                    $values[] = '("'.implode('","',$arrRow).'")';
                }
            }

            $rows = implode('`,`',$this->mapping);
            $insert = 'INSERT INTO 
                        '.$this->sTable.' 
                        (`'.$rows.'`) 
                        VALUES 
                        '.implode(',',$values);

            $db = \Database::getInstance();
            $db->query($insert);

            // first make backup copy and delete old backup table

            //delete old backup table
            $sDel = "DROP TABLE IF EXISTS " . $this->sBackupTable;
            $db = \Database::getInstance();
            $rDel = $db->execute($sDel);

            // duplicate livetable for backup
            $sCopy = "CREATE TABLE " . $this->sBackupTable . " AS SELECT * FROM " . $this->sTable;
            $rCopy = $db->execute($sCopy);

            // truncate livetable if backup table contains data
            $aCheckBackupTable = $db->execute("SELECT * from " . $this->sBackupTable)->fetchAllAssoc();
            $struncate = "DELETE FROM " . $this->sTable;

            if (count($aCheckBackupTable) > 0) {
                $rTruncate = $db->execute($struncate);
                $db->query($insert);
            } else {
                throw new \Exception('Cant create Backuptable');
            };


        } catch (\Exception $e) {
            $this->sMessage = $e->getMessage();
        }


        \Message::addInfo($this->sMessage);
        \Controller::redirect(\Controller::getReferer());
    }



    protected function addMapping(){

        $this->mapping = array(
          0 => 'code',
          1 => 'contactPerson',
          2 => 'postal',
          3 => '500',
          4 => '1000',
          5 => '1500',
          6 => '2000',
          7 => '2500',
          8 => '3000',
          9 => '3500',
          10 => '4000',
          11 => '4500',
          12 => '5000',
          13 => '6000',
          14 => '7000',
          15 => '8000',
          16 => '9000',
          17 => '10000',
          18 => '12000',
          19 => '14000',
          20 => '15000',
          21 => '16000',
          22 => '18000',
          23 => '20000',
          24 => '23000',
          25 => '26000',
          26 => '29000',
          27 => '32000',
          28 => 'economyOil',
          29 => 'purchasePrice',
          30 => 'additionalUnloadingPoint',
          31 => 'adrFlat',
          32 => 'deliveryExpressDefinition',
          33 => 'deliveryExpress',
          34 => 'deliveryStandardDefinition',
          35 => 'deliveryStandard',
          36 => 'deliveryPremiumDefinition',
          37 => 'deliveryPremium',
          38 => 'invoice',
          39 => 'zmzAccount',
          40 => 'ecCard',
          41 => 'debit',
          42 => 'cash',
          43 => 'hoseUpTo60',
          44 => 'hoseUpTo80',
          45 => 'specialTankTruckSmall',
          46 => 'antifreeze',
          47 => 'branch',
          48 => 'flex5',
          49 => 'flex6',
          50 => 'sellingPrice',
        );

    }
}