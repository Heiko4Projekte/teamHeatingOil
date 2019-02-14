<?php


namespace Slashworks\SwCalc\Models;


use Contao\Database;
use \Contao\Model;
use Slashworks\SwCalc\Models\Pricing;


class Label extends Model
{

    /**
     * @var
     */
    protected static $strLabel;

    /**
     * @var
     */
    protected $strLabelAlias;


    /**
     * @var string
     */
    protected static $strTable = 'tl_calc_label';


    /**
     * @param $alias
     *
     * @return \Model\Collection|null|static
     */
    public static function getLabel($alias)
    {

        self::$strLabel = Label::findBy('alias', $alias);
        if (null === self::$strLabel) {
            return false;
        }

        return self::$strLabel->value;

    }


    /**
     * @param $group
     *
     * @return array|bool
     */
    public static function getLabelGroup($group)
    {

        $query = "SELECT alias,value FROM " . self::$strTable . " WHERE alias like '$group.%'";
        $arrResult = \Database::getInstance()->prepare($query)->execute()->fetchAllAssoc();
        $labelGroup = array();

        if (null === $arrResult) {
            return false;
        }

        foreach ($arrResult as $item) {

            //extract groups
            $arrGroups = explode('.', $item['alias']);

            if (count($arrGroups) == 3) {
                $labelGroup[$arrGroups[1]][$arrGroups[2]] = $item['value'];
            } else {
                $labelGroup[$arrGroups[1]] = $item['value'];
            }
        }

        return $labelGroup;

    }


    /**
     * @param      $string
     * @param bool $field
     *
     * @return string
     * @throws \Exception
     */
    public static function getDynamicLabel($string, $field = false)
    {

        $oPricingRow = \Slashworks\SwCalc\Models\Pricing::findByPostal($_POST['postal']);

        if (null === $oPricingRow) {

            $config = \Slashworks\SwCalc\Models\Configuration::getActive();
            $GLOBALS['TL_CALC']['fallbackPostal'] = $config->fallbackPostal;
            $oPricingRow = \Slashworks\SwCalc\Models\Pricing::findByPostal($GLOBALS['TL_CALC']['fallbackPostal']);
        }

        switch ($field) {
            case 'deliveryStandard':
                $insert = $oPricingRow->deliveryStandardDefinition;
                break;
            case 'deliveryPremium':
                $insert = $oPricingRow->deliveryPremiumDefinition;
                break;
            case 'deliveryExpress':
                $insert = $oPricingRow->deliveryExpressDefinition;
                break;
            case 'team':
                $insert = self::getTeamMember();
                break;
            default:
                $insert = $field;
        }

        $return = sprintf($string, $insert);

        return $return;


    }


    /**
     * @param bool $oPricingRow
     *
     * @return bool|string
     * @throws \Exception
     */
    public static function getTeamMember($oPricingRow = false)
    {

        $dbConfig = array(
            'dbDriver'   => $GLOBALS['TL_CONFIG']['dbDriverExt'],
            'dbHost'     => $GLOBALS['TL_CONFIG']['dbHostExt'],
            'dbUser'     => $GLOBALS['TL_CONFIG']['dbUserExt'],
            'dbPass'     => $GLOBALS['TL_CONFIG']['dbPassExt'],
            'dbDatabase' => $GLOBALS['TL_CONFIG']['dbDatabaseExt'],
            'dbPconnect' => $GLOBALS['TL_CONFIG']['dbPconnectExt'],
            'dbCharset'  => $GLOBALS['TL_CONFIG']['dbCharsetExt'],
            'dbPort'     => $GLOBALS['TL_CONFIG']['dbPortExt'],
            'dbSocket'   => $GLOBALS['TL_CONFIG']['dbSocketExt'],
        );

        $db = Database::getInstance($dbConfig);
        $member = [];
        $aTeamMember = [];

        if ($oPricingRow) {
            $branch = $oPricingRow->branch;
        } else {

            $config = \Slashworks\SwCalc\Models\Configuration::getActive();
            $GLOBALS['TL_CALC']['fallbackPostal'] = $config->fallbackPostal;
            $branch = Pricing::findByPostal($GLOBALS['TL_CALC']['fallbackPostal'])->branch;
        }

        $memberSql = "SELECT * FROM tx_team_domain_model_employee WHERE branch = $branch";
        $aTeamMember = $db->query($memberSql)->fetchAllAssoc();


        if (count($aTeamMember) < 1) {
            return false;
        }

        foreach ($aTeamMember as  $k => $member){

            // get autoimage
            $path = '/fileadmin/user_upload/Mitarbeiter/';

            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $path . $member['email_address'] . '.jpg')) {
                $image = $path . $member['email_address'] . '.jpg';
            } elseif (file_exists($_SERVER['DOCUMENT_ROOT'] . $path . self::simplestring($member['first_name']) . '.' . self::simplestring($member['last_name']) . '@team.de.jpg')) {
                $image = $path . self::simplestring($member['first_name']) . '.' . self::simplestring($member['last_name']) . '@team.de.jpg';
            } else {
                $image = '/fileadmin/user_upload/team_dummy.png';
            }

            echo $member['emailAddress'];
            $aTeamMember[$k]['imagePath'] = \Image::get('../' . $image, 350, 230, '');
            $aTeamMember[$k]['testimage'] = $image;

        }

        $oTemplate = new \FrontendTemplate('teamMember');
        $oTemplate->data = $aTeamMember;

        return $oTemplate->parse();
    }


    /**
     * @return string
     */
    private static function simplestring($string)
    {
        $string = str_replace("ä", "ae", $string);
        $string = str_replace("ü", "ue", $string);
        $string = str_replace("ö", "oe", $string);
        $string = str_replace("ß", "ss", $string);
        $string = str_replace("´", "", $string);

        return strtolower($string);
    }


}