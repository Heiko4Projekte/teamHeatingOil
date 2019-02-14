<?php

namespace Slashworks\SwCalc\Backend;

use Contao\BackendTemplate;
use Contao\Controller;
use Contao\Database;
use Contao\Date;
use Contao\File;
use Contao\FormSubmit;
use Contao\Input;
use Contao\Model;
use Contao\SelectMenu;
use Contao\StringUtil;
use Contao\System;
use Contao\TextField;
use Contao\TimePeriod;
use Haste\IO\Mapper\ArrayMapper;
use Haste\IO\Reader\ModelCollectionReader;
use Haste\IO\Writer\CsvFileWriter;
use Haste\IO\Writer\ExcelFileWriter;
use Haste\Number\Number;
use Slashworks\SwCalc\Classes\Formatter;

class ExportCsv
{

    /**
     * @var BackendTemplate
     */
    protected $template;

    protected $formSubmit = 'form_submit_exportcsv';

    public function exportCsv()
    {
        $this->template = new BackendTemplate('be_exportcsv');

        $this->template->href = System::getReferer(true);
        $this->template->title = specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']);
        $this->template->button = $GLOBALS['TL_LANG']['MSC']['backBT'];
        $this->template->formSubmit = $this->formSubmit;

        $this->handleFormSubmit();

        $this->createForm();

        return Controller::replaceInsertTags($this->template->parse());
    }

    protected function handleFormSubmit()
    {
        if (Input::post('FORM_SUBMIT') === $this->formSubmit) {
            $start = Input::post('start');
            $stop = Input::post('stop');
            $fileformat = Input::post('export_file_format');

            // This columns are prices and should be formatted.
            $aPriceFieldsToFormat = array
            (
                'total',
                'totalPer100',
                'sepaMonthlyPrice',
                'subTotal',
                'adr',
                'vat',
                'subTotalPerAmount',
                'antifreezeSurcharge',
                'antifreezePrice',
                'subTotalPer100',
            );

            $aColumns = array('type = "order"');
            $aOptions = array();

            if ($start) {
                $oStart = new Date($start, 'd.m.Y');
                $aColumns[] = 'tstamp >= ' . $oStart->tstamp;
            }
            if ($stop) {
                $oStop = new Date($stop, 'd.m.Y');
                $aColumns[] = 'tstamp <= ' . $oStop->tstamp;
            }

            // Sort from newest to oldest collection.
            $aOptions['order'] = 'tstamp DESC';

            /** @var Model $oResults */
            $oResults = \Slashworks\SwCalc\Models\Collection::findBy($aColumns, null, $aOptions);

            if ($oResults !== null) {
                $today = Date::parse('d.m.Y-H.i');
                $sFileName = 'heizoelrechner-bestellungen-' . $today . '.' . $fileformat;

                $oReader = new ModelCollectionReader($oResults);
                $oReader->setHeaderFields(Database::getInstance()->getFieldNames('tl_calc_collection'));

                $oWriter = null;

                if ($fileformat === 'csv') {
                    $oWriter = new CsvFileWriter();
                    $oWriter->enableHeaderFields();
                    $oWriter->setDelimiter(';');
                    $oWriter->setEnclosure('"');
                } else if ($fileformat === 'xlsx') {
                    $oWriter = new ExcelFileWriter();
                    $oWriter->enableHeaderFields();
                }

                if ($oWriter === null) {
                    return;
                }

                $oWriter->setRowCallback(function ($aRow) use ($aPriceFieldsToFormat) {
                    $sTimestamp = $aRow['tstamp'];
                    $sReadableTime = Date::parse('d.m.Y - H:i', $sTimestamp);
                    $aRow['tstamp'] = $sReadableTime;

                    // Format price rows to german format, including rounding.
                    foreach ($aPriceFieldsToFormat as $sColumn) {
                        $aRow[$sColumn] = Formatter::formatPrice($aRow[$sColumn]);
                    }

                    return $aRow;
                });

                $oWriter->writeFrom($oReader);

                $oFile = new File($oWriter->getFilename());
                $oFile->sendToBrowser($sFileName);
            }
        }
    }

    protected function createForm()
    {
        // Define form fields.
        $aSubmitField = array
        (
            'name'   => 'exportcsv_submit',
            'id'     => 'exportcsv_submit',
            'class'  => 'tl_submit',
            'slabel' => 'Export starten',
        );
        $aStartField = array
        (
            'name'  => 'start',
            'id'    => 'start',
            'value' => Input::post('start'),
        );
        $aStopField = array
        (
            'name'  => 'stop',
            'id'    => 'stop',
            'value' => Input::post('stop'),
        );
        $aFileFormatField = array
        (
            'name'  => 'export_file_format',
            'id'    => 'export_file_format',
            'options' => array
            (
                array('value' => 'xlsx', 'label' => '.xslx'),
                array('value' => 'csv', 'label' => '.csv'),
            ),
            'value' => Input::post('export_file_format'),
        );

        // Generate widgets from form fields.
        $oStartWidget = new TextField($aStartField);
        $oStopWidget = new TextField($aStopField);
        $oFileFormatWidget = new SelectMenu($aFileFormatField);
        $oSubmitWidget = new FormSubmit($aSubmitField);

        // Transfer rendered widgets to template.
        $this->template->startField = $oStartWidget->generate();
        $this->template->startFieldDatePicker = $this->createDatePicker($oStartWidget);

        $this->template->stopField = $oStopWidget->generate();
        $this->template->stopFieldDatePicker = $this->createDatePicker($oStopWidget);

        $this->template->fileFormatField = $oFileFormatWidget->generate();

        $this->template->submitField = $oSubmitWidget->generate();
    }

    protected function createDatePicker($oWidget)
    {
        $sDatePicker = \Image::getHtml('assets/mootools/datepicker/' . $GLOBALS['TL_ASSETS']['DATEPICKER'] . '/icon.gif',
            '',
            'title="' . specialchars($GLOBALS['TL_LANG']['MSC']['datepicker']) . '" id="toggle_' . $oWidget->id . '" style="vertical-align:-6px;cursor:pointer"');

        $sDatePicker .= '<script>
        window.addEvent("domready", function() {
          new Picker.Date($("ctrl_' . $oWidget->id . '"), {
            draggable: false,
            toggle: $("toggle_' . $oWidget->id . '"),
            format: "%d.%m.%Y",
            positionOffset: {x:-211,y:-209},
            pickerClass: "datepicker_bootstrap",
            useFadeInOut: !Browser.ie,
            startDay: ' . $GLOBALS['TL_LANG']['MSC']['weekOffset'] . ',
            titleFormat: "' . $GLOBALS['TL_LANG']['MSC']['titleFormat'] . '"
          });
        });
        </script>';

        return $sDatePicker;
    }

}