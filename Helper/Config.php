<?php
/**
 * ClassyLlama_AvaTax
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2016 Avalara, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace ClassyLlama\AvaTax\Helper;

use AvaTax\ATConfigFactory;
use ClassyLlama\AvaTax\Framework\AppInterface as AvaTaxAppInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Phrase;
use Magento\Shipping\Model\Config as ShippingConfig;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Framework\App\State;
use Magento\Tax\Api\TaxClassRepositoryInterface;

/**
 * AvaTax Config model
 */
class Config extends AbstractHelper
{
    /**#@+
     * Module config settings
     */
    const XML_PATH_AVATAX_MODULE_ENABLED = 'tax/avatax/enabled';

    const XML_PATH_AVATAX_TAX_MODE = 'tax/avatax/tax_mode';

    const XML_PATH_AVATAX_COMMIT_SUBMITTED_TRANSACTIONS = 'tax/avatax/commit_submitted_transactions';

    const XML_PATH_AVATAX_TAX_CALCULATION_COUNTRIES_ENABLED = 'tax/avatax/tax_calculation_countries_enabled';

    const XML_PATH_AVATAX_FILTER_TAX_BY_REGION = 'tax/avatax/filter_tax_by_region';

    const XML_PATH_AVATAX_REGION_FILTER_LIST = 'tax/avatax/region_filter_list';

    const XML_PATH_AVATAX_LIVE_MODE = 'tax/avatax/live_mode';

    const XML_PATH_AVATAX_PRODUCTION_ACCOUNT_NUMBER = 'tax/avatax/production_account_number';

    const XML_PATH_AVATAX_PRODUCTION_LICENSE_KEY = 'tax/avatax/production_license_key';

    const XML_PATH_AVATAX_PRODUCTION_COMPANY_CODE = 'tax/avatax/production_company_code';

    const XML_PATH_AVATAX_DEVELOPMENT_ACCOUNT_NUMBER = 'tax/avatax/development_account_number';

    const XML_PATH_AVATAX_DEVELOPMENT_LICENSE_KEY = 'tax/avatax/development_license_key';

    const XML_PATH_AVATAX_DEVELOPMENT_COMPANY_CODE = 'tax/avatax/development_company_code';

    const XML_PATH_AVATAX_CUSTOMER_CODE_FORMAT = 'tax/avatax/customer_code_format';

    const XML_PATH_AVATAX_SKU_SHIPPING = 'tax/avatax/sku_shipping';

    const XML_PATH_AVATAX_SKU_GIFT_WRAP_ORDER = 'tax/avatax/sku_gift_wrap_order';

    const XML_PATH_AVATAX_SKU_GIFT_WRAP_ITEM = 'tax/avatax/sku_gift_wrap_item';

    const XML_PATH_AVATAX_SKU_GIFT_WRAP_CARD = 'tax/avatax/sku_gift_wrap_card';

    const XML_PATH_AVATAX_SKU_ADJUSTMENT_POSITIVE = 'tax/avatax/sku_adjustment_positive';

    const XML_PATH_AVATAX_SKU_ADJUSTMENT_NEGATIVE = 'tax/avatax/sku_adjustment_negative';

    const XML_PATH_AVATAX_SKU_LOCATION_CODE = 'tax/avatax/location_code';

    const XML_PATH_AVATAX_UPC_ATTRIBUTE = 'tax/avatax/upc_attribute';

    const XML_PATH_AVATAX_REF1_ATTRIBUTE = 'tax/avatax/ref1_attribute';

    const XML_PATH_AVATAX_REF2_ATTRIBUTE = 'tax/avatax/ref2_attribute';

    const XML_PATH_AVATAX_USE_VAT = 'tax/avatax/use_business_identification_number';

    const XML_PATH_AVATAX_ERROR_ACTION = 'tax/avatax/error_action';

    const XML_PATH_AVATAX_ERROR_ACTION_DISABLE_CHECKOUT_MESSAGE_FRONTEND = 'tax/avatax/error_action_disable_checkout_message_frontend';

    const XML_PATH_AVATAX_ERROR_ACTION_DISABLE_CHECKOUT_MESSAGE_BACKEND = 'tax/avatax/error_action_disable_checkout_message_backend';

    const XML_PATH_AVATAX_ADDRESS_VALIDATION_ENABLED = "tax/avatax/address_validation_enabled";

    const XML_PATH_AVATAX_ADDRESS_VALIDATION_METHOD = "tax/avatax/address_validation_user_has_choice";

    const XML_PATH_AVATAX_ADDRESS_VALIDATION_COUNTRIES_ENABLED = "tax/avatax/address_validation_countries_enabled";

    const XML_PATH_AVATAX_ADDRESS_VALIDATION_INSTRUCTIONS_WITH_CHOICE = "tax/avatax/address_validation_instructions_with_choice";

    const XML_PATH_AVATAX_ADDRESS_VALIDATION_INSTRUCTIONS_WITHOUT_CHOICE = "tax/avatax/address_validation_instructions_without_choice";

    const XML_PATH_AVATAX_ADDRESS_VALIDATION_ERROR_INSTRUCTIONS = "tax/avatax/address_validation_error_instructions";

    const XML_PATH_AVATAX_LOG_DB_LEVEL = 'tax/avatax/logging_db_level';

    const XML_PATH_AVATAX_LOG_DB_DETAIL = 'tax/avatax/logging_db_detail';

    const XML_PATH_AVATAX_LOG_DB_LIFETIME = 'tax/avatax/logging_db_lifetime';

    const XML_PATH_AVATAX_LOG_FILE_ENABLED = 'tax/avatax/logging_file_enabled';

    const XML_PATH_AVATAX_LOG_FILE_MODE = 'tax/avatax/logging_file_mode';

    const XML_PATH_AVATAX_LOG_BUILTIN_ROTATE_ENABLED = 'tax/avatax/logging_file_builtin_rotation_enabled';

    const XML_PATH_AVATAX_LOG_BUILTIN_ROTATE_MAX_FILES = 'tax/avatax/logging_file_builtin_rotation_max_files';

    const XML_PATH_AVATAX_LOG_FILE_LEVEL = 'tax/avatax/logging_file_level';

    const XML_PATH_AVATAX_LOG_FILE_DETAIL = 'tax/avatax/logging_file_detail';

    const XML_PATH_AVATAX_QUEUE_MAX_RETRY_ATTEMPTS = 'tax/avatax/queue_max_retry_attempts';

    const XML_PATH_AVATAX_QUEUE_COMPLETE_LIFETIME = 'tax/avatax/queue_complete_lifetime';

    const XML_PATH_AVATAX_QUEUE_FAILED_LIFETIME = 'tax/avatax/queue_failed_lifetime';

    const XML_PATH_AVATAX_QUEUE_ADMIN_NOTIFICATION_ENABLED = 'tax/avatax/queue_admin_notification_enabled';

    const XML_PATH_AVATAX_ADMIN_NOTIFICATION_IGNORE_NATIVE_TAX_RULES = 'tax/avatax/ignore_native_tax_rules_notification';

    const XML_PATH_AVATAX_ADMIN_IS_SELLER_IMPORTER_OF_RECORD = 'tax/avatax/is_seller_importer_of_record';
    /**#@-*/

    /**
     * List of countries that are enabled by default
     */
    static public $taxCalculationCountriesDefault = ['US', 'CA'];

    /**#@+
     * Customer Code Format Options
     */
    const CUSTOMER_FORMAT_OPTION_EMAIL = 'email';

    const CUSTOMER_FORMAT_OPTION_ID = 'id';

    const CUSTOMER_FORMAT_OPTION_NAME_ID = 'name_id';
    /**#@-*/

    /**
     * Customer Code Format for "name_id" option
     */
    const CUSTOMER_FORMAT_NAME_ID = '%s (%s)';

    /**
     * If user is guest, ID to use for "name_id" option
     */
    const CUSTOMER_GUEST_ID = 'Guest';

    /**
     * Value to send as "customer_code" if "email" is selected and quote doesn't have email
     */
    const CUSTOMER_MISSING_EMAIL = 'No email';

    /**
     * Value to send as "customer_code" if "name_id" is selected and quote doesn't have name
     */
    const CUSTOMER_MISSING_NAME = 'No name';

    /**#@+
     * Error Action Options
     */
    const ERROR_ACTION_DISABLE_CHECKOUT = 1;

    const ERROR_ACTION_ALLOW_CHECKOUT_NATIVE_TAX = 2;
    /**#@-*/

    /**#@+
     * Tax Modes
     */
    const TAX_MODE_NO_ESTIMATE_OR_SUBMIT = 1;

    const TAX_MODE_ESTIMATE_ONLY = 2;

    const TAX_MODE_ESTIMATE_AND_SUBMIT = 3;
    /**#@-*/

    /**#@+
     * AvaTax API values
     */
    const API_URL_DEV = 'https://development.avalara.net';

    const API_URL_PROD = 'https://avatax.avalara.net';

    const API_PROFILE_NAME_DEV = 'Development';

    const API_PROFILE_NAME_PROD = 'Production';
    /**#@-*/

    const AVATAX_DOCUMENTATION_TAX_CODE_LINK
        = 'https://help.avalara.com/000_AvaTax_Calc/000AvaTaxCalc_User_Guide/051_Select_AvaTax_System_Tax_Codes/Tax_Codes_-_Frequently_Asked_Questions';

    /**
     * Magento version prefix
     */
    const API_APP_NAME_PREFIX = 'Magento 2';

    /**
     * Cache tag code
     */
    const AVATAX_CACHE_TAG = 'AVATAX';

    /**
     * @var ProductMetadataInterface
     */
    protected $magentoProductMetadata = null;

    /**
     * @var ATConfigFactory
     */
    protected $avaTaxConfigFactory = null;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @var TaxClassRepositoryInterface
     */
    protected $taxClassRepository = null;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $backendUrl;

    /**
     * Class constructor
     *
     * @param Context $context
     * @param ProductMetadataInterface $magentoProductMetadata
     * @param ATConfigFactory $avaTaxConfigFactory
     * @param State $appState
     * @param TaxClassRepositoryInterface $taxClassRepository
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     */
    public function __construct(
        Context $context,
        ProductMetadataInterface $magentoProductMetadata,
        ATConfigFactory $avaTaxConfigFactory,
        State $appState,
        TaxClassRepositoryInterface $taxClassRepository,
        \Magento\Backend\Model\UrlInterface $backendUrl
    ) {
        $this->magentoProductMetadata = $magentoProductMetadata;
        $this->avaTaxConfigFactory = $avaTaxConfigFactory;
        $this->appState = $appState;
        $this->taxClassRepository = $taxClassRepository;
        $this->backendUrl = $backendUrl;
        parent::__construct($context);
    }

    /**
     * Create a profile based on the store ID
     *
     * @param $storeId
     * @param $scopeType
     */
    public function createAvaTaxProfile($storeId, $scopeType = ScopeInterface::SCOPE_STORE)
    {
        if ($this->getLiveMode($storeId, $scopeType)) {
            $this->avaTaxConfigFactory->create(
                [
                    'name' => self::API_PROFILE_NAME_PROD,
                    'values' => [
                        'url'       => self::API_URL_PROD,
                        'account'   => $this->getAccountNumber($storeId, $scopeType),
                        'license'   => $this->getLicenseKey($storeId, $scopeType),
                        'trace'     => false,
                        'client' => $this->getClientName(),
                        'name' => self::API_PROFILE_NAME_PROD,
                    ],
                ]
            );
        } else {
            $this->avaTaxConfigFactory->create(
                [
                    'name' => self::API_PROFILE_NAME_DEV,
                    'values' => [
                        'url'       => self::API_URL_DEV,
                        'account'   => $this->getDevelopmentAccountNumber($storeId, $scopeType),
                        'license'   => $this->getDevelopmentLicenseKey($storeId, $scopeType),
                        'trace'     => true,
                        'client' => $this->getClientName(),
                        'name' => self::API_PROFILE_NAME_DEV,
                    ],
                ]
            );
        }

    }

    /**
     * Return whether module is enabled
     *
     * @param null $store
     * @param $scopeType
     * @return mixed
     */
    public function isModuleEnabled($store = null, $scopeType = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_MODULE_ENABLED,
            $scopeType,
            $store
        );
    }

    /**
     * Return tax mode
     *
     * @param $store
     * @return mixed
     */
    public function getTaxMode($store)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_TAX_MODE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Return whether to commit submitted transactions
     *
     * @param $store
     * @return mixed
     */
    public function getCommitSubmittedTransactions($store)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_COMMIT_SUBMITTED_TRANSACTIONS,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param $store
     * @param $scopeType
     * @return mixed
     */
    public function getTaxCalculationCountriesEnabled($store, $scopeType = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_TAX_CALCULATION_COUNTRIES_ENABLED,
            $scopeType,
            $store
        );
    }

    /**
     * @param $store
     * @return mixed
     */
    protected function getFilterTaxByRegion($store)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_FILTER_TAX_BY_REGION,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param $store
     * @return mixed
     */
    protected function getRegionFilterList($store)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_REGION_FILTER_LIST,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Determine whether address is taxable, based on either country or region
     *
     * @param \Magento\Framework\DataObject $address
     * @param $storeId
     * @return bool
     */
    public function isAddressTaxable(\Magento\Framework\DataObject $address, $storeId)
    {
        $isTaxable = true;
        // Filtering just by country (not region)
        if (!$this->getFilterTaxByRegion($storeId)) {
            $countryFilters = explode(',', $this->getTaxCalculationCountriesEnabled($storeId));
            $countryId = $address->getCountryId();
            if (!in_array($countryId, $countryFilters)) {
                $isTaxable = false;
            }
        // Filtering by region within countries
        } else {
            $regionFilters = explode(',', $this->getRegionFilterList($storeId));
            $entityId = $address->getRegionId() ?: $address->getCountryId();
            if (!in_array($entityId, $regionFilters)) {
                $isTaxable = false;
            }
        }
        return $isTaxable;
    }

    /**
     * Return origin address
     *
     * @param null $store
     * @return array
     */
    public function getOriginAddress($store)
    {
        return [
            'Line1' => $this->scopeConfig->getValue(
                // Line1 and Line2 constants are missing from \Magento\Shipping\Model\Config, so using them from Shipment
                \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ADDRESS1,
                ScopeInterface::SCOPE_STORE,
                $store
            ),
            'Line2' => $this->scopeConfig->getValue(
                \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ADDRESS2,
                ScopeInterface::SCOPE_STORE,
                $store
            ),
            'City' => $this->scopeConfig->getValue(
                ShippingConfig::XML_PATH_ORIGIN_CITY,
                ScopeInterface::SCOPE_STORE,
                $store
            ),
            'RegionId' => $this->scopeConfig->getValue(
                ShippingConfig::XML_PATH_ORIGIN_REGION_ID,
                ScopeInterface::SCOPE_STORE,
                $store
            ),
            'PostalCode' => $this->scopeConfig->getValue(
                ShippingConfig::XML_PATH_ORIGIN_POSTCODE,
                ScopeInterface::SCOPE_STORE,
                $store
            ),
            'Country' => $this->scopeConfig->getValue(
                ShippingConfig::XML_PATH_ORIGIN_COUNTRY_ID,
                ScopeInterface::SCOPE_STORE,
                $store
            ),
        ];
    }

    /**
     * Get Customer code format to pass to AvaTax API
     *
     * @param $store
     * @return mixed
     */
    public function getCustomerCodeFormat($store)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_CUSTOMER_CODE_FORMAT,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Generate AvaTax Client Name from a combination of Magento version number and AvaTax module version number
     * Format: Magento 2.x Community - AvaTax 1.0.0
     * Limited to 50 characters to comply with API requirements
     *
     * @return string
     */
    protected function getClientName()
    {
        return substr($this->magentoProductMetadata->getName(), 0, 7) . ' ' . // "Magento" - 8 chars
            substr($this->magentoProductMetadata->getVersion(), 0, 14) . ' ' . // 2.x & " " - 50 - 8 - 13 - 14 = 15 chars
            substr($this->magentoProductMetadata->getEdition(), 0, 10) . ' - ' . // "Community - "|"Enterprise - " - 13 chars
            'AvaTax ' . substr(AvaTaxAppInterface::APP_VERSION, 0, 7); // "AvaTax " & 1.x.x - 14 chars
    }

    /**
     * Get Live vs. Development mode of the module
     *
     * Must be configured at default level as it is difficult to pass store in all contexts this is used
     *
     * @param $store
     * @param string $scopeType
     * @return bool
     */
    public function getLiveMode($store, $scopeType = ScopeInterface::SCOPE_STORE)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_LIVE_MODE,
            $scopeType,
            $store
        );
    }

    /**
     * Get account number from config
     *
     * @param $store
     * @param $scopeType
     * @return string
     */
    public function getAccountNumber($store, $scopeType = ScopeInterface::SCOPE_STORE)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_PRODUCTION_ACCOUNT_NUMBER,
            $scopeType,
            $store
        );
    }

    /**
     * Get license key from config
     *
     * @param $store
     * @param $scopeType
     * @return string
     */
    public function getLicenseKey($store, $scopeType = ScopeInterface::SCOPE_STORE)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_PRODUCTION_LICENSE_KEY,
            $scopeType,
            $store
        );
    }

    /**
     * Get company code from config
     *
     * @param $store
     * @return string
     */
    public function getCompanyCode($store, $scopeType = ScopeInterface::SCOPE_STORE)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_PRODUCTION_COMPANY_CODE,
            $scopeType,
            $store
        );
    }

    /**
     * Get development account number from config
     *
     * @param $store
     * @param $scopeType
     * @return string
     */
    public function getDevelopmentAccountNumber($store, $scopeType = ScopeInterface::SCOPE_STORE)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_DEVELOPMENT_ACCOUNT_NUMBER,
            $scopeType,
            $store
        );
    }

    /**
     * Get development license key from config
     *
     * @param $store
     * @param $scopeType
     * @return string
     */
    public function getDevelopmentLicenseKey($store, $scopeType = ScopeInterface::SCOPE_STORE)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_DEVELOPMENT_LICENSE_KEY,
            $scopeType,
            $store
        );
    }

    /**
     * Get development company code from config
     *
     * @param $store
     * @param $scopeType
     * @return string
     */
    public function getDevelopmentCompanyCode($store, $scopeType = ScopeInterface::SCOPE_STORE)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_DEVELOPMENT_COMPANY_CODE,
            $scopeType,
            $store
        );
    }

    /**
     * Get SKU for Shipping
     *
     * @param $store
     * @return string
     */
    public function getSkuShipping($store)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_SKU_SHIPPING,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get SKU for Gift Wrap at the Order Level
     *
     * @param $store
     * @return string
     */
    public function getSkuGiftWrapOrder($store)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_SKU_GIFT_WRAP_ORDER,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get SKU for Gift Wrap at the Item Level
     *
     * @param $store
     * @return string
     */
    public function getSkuShippingGiftWrapItem($store)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_SKU_GIFT_WRAP_ITEM,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get SKU for Gift Wrap card
     *
     * @param $store
     * @return string
     */
    public function getSkuShippingGiftWrapCard($store)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_SKU_GIFT_WRAP_CARD,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get SKU for positive adjustment
     *
     * @param $store
     * @return string
     */
    public function getSkuAdjustmentPositive($store)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_SKU_ADJUSTMENT_POSITIVE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get SKU for negative adjustment
     *
     * @param $store
     * @return string
     */
    public function getSkuAdjustmentNegative($store)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_SKU_ADJUSTMENT_NEGATIVE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get Location Code
     *
     * @param $store
     * @return string
     */
    public function getLocationCode($store)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_SKU_LOCATION_CODE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get UPC configured attribute code
     *
     * @return string
     */
    public function getUpcAttribute()
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_AVATAX_UPC_ATTRIBUTE);
    }

    /**
     * Get ref1 configured attribute code
     *
     * @return string
     */
    public function getRef1Attribute()
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_AVATAX_REF1_ATTRIBUTE);
    }

    /**
     * Get ref2 configured attribute code
     *
     * @return string
     */
    public function getRef2Attribute()
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_AVATAX_REF2_ATTRIBUTE);
    }

    /**
     * Get whether should use Business Identification Number (VAT)
     *
     * @param $store
     * @return string
     */
    public function getUseBusinessIdentificationNumber($store)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_USE_VAT,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get action to take when error occurs
     *
     * @param $store
     * @return string
     */
    public function getErrorAction($store)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_ERROR_ACTION,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Return "disable checkout" error message based on the current area context
     *
     * @param $store
     * @return \Magento\Framework\Phrase
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getErrorActionDisableCheckoutMessage($store)
    {
        if ($this->appState->getAreaCode() == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
            return __(
                $this->getErrorActionDisableCheckoutMessageBackend($store),
                $this->backendUrl->getUrl('admin/system_config/edit', ['section' => 'tax']),
                $this->backendUrl->getUrl('avatax/log')
            );
        } else {
            return __($this->getErrorActionDisableCheckoutMessageFrontend($store));
        }
    }

    /**
     * Get "disable checkout" error message for frontend user
     *
     * @param $store
     * @return string
     */
    protected function getErrorActionDisableCheckoutMessageFrontend($store)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_ERROR_ACTION_DISABLE_CHECKOUT_MESSAGE_FRONTEND,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get "disable checkout" error message for backend user
     *
     * @param $store
     * @return string
     */
    protected function getErrorActionDisableCheckoutMessageBackend($store)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_ERROR_ACTION_DISABLE_CHECKOUT_MESSAGE_BACKEND,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Return if address validation is enabled
     *
     * @param null $store
     * @return mixed
     */
    public function isAddressValidationEnabled($store)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_ADDRESS_VALIDATION_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Returns if user is allowed to choose between the original address and the validated address
     *
     * @param null $store
     * @return mixed
     */
    public function allowUserToChooseAddress($store)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_ADDRESS_VALIDATION_METHOD,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Instructions for the user if they have a choice between the original address and validated address
     *
     * @param $store
     * @return string
     */
    public function getAddressValidationInstructionsWithChoice($store)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_ADDRESS_VALIDATION_INSTRUCTIONS_WITH_CHOICE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Instructions for the user if they do not have a choice between the original address and the validated address
     *
     * @param $store
     * @return string
     */
    public function getAddressValidationInstructionsWithoutChoice($store)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_ADDRESS_VALIDATION_INSTRUCTIONS_WITHOUT_CHOICE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Instructions for the user if there was an error in validating their address
     *
     * @param $store
     * @return string
     */
    public function getAddressValidationErrorInstructions($store)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_ADDRESS_VALIDATION_ERROR_INSTRUCTIONS,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Returns which countries were enabled to validate the users address
     *
     * @param $store
     * @return mixed
     */
    public function getAddressValidationCountriesEnabled($store)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AVATAX_ADDRESS_VALIDATION_COUNTRIES_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Return configured log level
     *
     * @return int
     */
    public function getLogDbLevel()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AVATAX_LOG_DB_LEVEL);
    }

    /**
     * Return configured log detail
     *
     * @return int
     */
    public function getLogDbDetail()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AVATAX_LOG_DB_DETAIL);
    }

    /**
     * Return configured log lifetime
     *
     * @return int
     */
    public function getLogDbLifetime()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AVATAX_LOG_DB_LIFETIME);
    }

    /**
     * Return if file logging is enabled
     *
     * @return bool
     */
    public function getLogFileEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AVATAX_LOG_FILE_ENABLED);
    }

    /**
     * Return configured log mode
     *
     * @return int
     */
    public function getLogFileMode()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AVATAX_LOG_FILE_MODE);
    }

    /**
     * Return if built-in log file rotation is enabled
     *
     * @return int
     */
    public function getLogFileBuiltinRotateEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AVATAX_LOG_BUILTIN_ROTATE_ENABLED);
    }

    /**
     * Return the number of built-in log files to maintain in the log directory when rotating files
     *
     * @return int
     */
    public function getLogFileBuiltinRotateMaxFiles()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AVATAX_LOG_BUILTIN_ROTATE_MAX_FILES);
    }

    /**
     * Return configured log level
     *
     * @return int
     */
    public function getLogFileLevel()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AVATAX_LOG_FILE_LEVEL);
    }

    /**
     * Return configured log detail
     *
     * @return int
     */
    public function getLogFileDetail()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AVATAX_LOG_FILE_DETAIL);
    }

    /**
     * Return configured queue max retry attempts
     *
     * @return int
     */
    public function getQueueMaxRetryAttempts()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AVATAX_QUEUE_MAX_RETRY_ATTEMPTS);
    }

    /**
     * Return configured queue complete lifetime
     *
     * @return int
     */
    public function getQueueCompleteLifetime()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AVATAX_QUEUE_COMPLETE_LIFETIME);
    }

    /**
     * Return configured queue failed lifetime
     *
     * @return int
     */
    public function getQueueFailedLifetime()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AVATAX_QUEUE_FAILED_LIFETIME);
    }

    /**
     * Return if queue admin notification is enabled
     *
     * @return int
     */
    public function getQueueAdminNotificationEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AVATAX_QUEUE_ADMIN_NOTIFICATION_ENABLED);
    }

    /**
     * Return if queue failure notification is enabled
     *
     * @return int
     */
    public function getQueueFailureNotificationEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AVATAX_QUEUE_FAILED_LIFETIME);
    }

    public function isNativeTaxRulesIgnored()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AVATAX_ADMIN_NOTIFICATION_IGNORE_NATIVE_TAX_RULES);
    }

    /**
     * Determine whether to set IsSellerImportOfRecord flag
     *
     * @param array $originAddress
     * @param \AvaTax\Address $destAddress
     * @param $storeId
     * @return bool
     */
    public function isSellerImporterOfRecord($originAddress, $destAddress, $storeId)
    {
        $isSellerImporterOfRecord = true;
        $countryFilters = explode(',', $this->getTaxCalculationCountriesEnabled($storeId));
        $originCountryId = (isset($originAddress['Country']) ? $originAddress['Country'] : false);
        $destCountryId = $destAddress->getCountry();
        if ($destCountryId == $originCountryId || !in_array($destCountryId, $countryFilters)) {
            $isSellerImporterOfRecord = false;
        }
        return $isSellerImporterOfRecord;
    }
}
