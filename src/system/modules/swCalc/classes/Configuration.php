<?php

namespace Slashworks\SwCalc\Classes;

class Configuration
{

  public static function getConfigurationData($oConfiguration)
  {
    return array
    (
      'title' => $oConfiguration->title,
      'amount' => $oConfiguration->amount,
      'shipping' => $oConfiguration->shipping,
      'payment' => $oConfiguration->payment,
      'hose' => $oConfiguration->hose,
      'oilType' => $oConfiguration->oilType,
      'labelGroup' => $oConfiguration->labelGroup,
      'uspField' => $oConfiguration->uspField,
      'postal' => $oConfiguration->postal,
      'unloadingPoints' => $oConfiguration->unloadingPoints
    );
  }

  public function getConfirmationTemplates()
  {
    return \Controller::getTemplateGroup('confirmation');
  }

}