<?php

/**
 * Isotope eCommerce for Contao Open Source CMS
 *
 * Copyright (C) 2009-2014 terminal42 gmbh & Isotope eCommerce Workgroup
 *
 * @package    Isotope
 * @link       http://isotopeecommerce.org
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */

namespace Isotope\Collection;

use Isotope\Interfaces\IsotopePrice;
use Isotope\Isotope;

class ProductPrice extends \Model\Collection implements IsotopePrice
{

    /**
     * Return true if more than one price is available
     * @return  bool
     */
    public function hasTiers()
    {
        return $this->current()->hasTiers();
    }

    /**
     * Return lowest tier (= minimum quantity)
     * @return  int
     */
    public function getLowestTier()
    {
        return $this->current()->getLowestTier();
    }

    /**
     * Return price
     * @param   int
     * @return  float
     */
    public function getAmount($intQuantity = 1)
    {
        return $this->current()->getAmount($intQuantity);
    }

    /**
     * Return original price
     * @param   int
     * @return  float
     */
    public function getOriginalAmount($intQuantity = 1)
    {
        return $this->current()->getOriginalAmount($intQuantity);
    }

    /**
     * Return net price (without taxes)
     * @param   int
     * @return  float
     */
    public function getNetAmount($intQuantity = 1)
    {
        return $this->current()->getNetAmount($intQuantity);
    }

    /**
     * Return gross price (with all taxes)
     * @param   int
     * @return  float
     */
    public function getGrossAmount($intQuantity = 1)
    {
        return $this->current()->getGrossAmount($intQuantity);
    }

    /**
     * Generate price for HTML rendering
     * @param   bool
     * @return  string
     */
    public function generate($blnShowTiers=false)
    {
        if (count($this->arrModels) > 1) {

            $fltPrice           = null;
            $fltOriginalPrice   = null;
            $arrPrices          = array();

            foreach ($this->arrModels as $objPrice) {
                $fltNew       = $blnShowTiers ? $objPrice->getLowestAmount() : $objPrice->getAmount();
                $arrPrices[]  = $fltNew;

                if (null === $fltPrice || $fltNew < $fltPrice) {
                    $fltPrice         = $fltNew;
                    $fltOriginalPrice = $objPrice->getOriginalAmount();
                }
            }

            $arrPrices = array_unique($arrPrices);
            $blnShowFrom = count($arrPrices) > 1;

            if ($blnShowFrom) {
                return sprintf($GLOBALS['TL_LANG']['MSC']['priceRangeLabel'], Isotope::formatPriceWithCurrency($fltPrice));
            } elseif ($fltPrice < $fltOriginalPrice) {
                $strPrice         = Isotope::formatPriceWithCurrency($fltPrice);
                $strOriginalPrice = Isotope::formatPriceWithCurrency($fltOriginalPrice);

                return '<div class="original_price"><strike>' . $strOriginalPrice . '</strike></div><div class="price">' . $strPrice . '</div>';
            } else {
                return Isotope::formatPriceWithCurrency($fltPrice);
            }

        } else {
            return $this->current()->generate($blnShowTiers);
        }
    }
}
