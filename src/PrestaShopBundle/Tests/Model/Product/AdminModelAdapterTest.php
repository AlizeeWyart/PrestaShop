<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Tests\Model\Product;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use PrestaShopBundle\Model\Product\AdminModelAdapter;
use PrestaShop\PrestaShop\Adapter\CombinationDataProvider;

class AdminModelAdapterTest extends KernelTestCase
{
    /* @var $adminModelAdapter AdminModelAdapter */
    private $adminModelAdapter;

    private $container;
    protected static $kernel;

    /* @var $product \ProductCore */
    private $product;

    private function fakeFormData()
    {
        return [
            "id_product" => '',
            "step1" => [
                "type_product" => '',
                "inputPackItems" => [],
                "name" => [],
                "name_1" => "Amazing product",
                "name_2" => "Amazing product",
                "description" => [],
                "description_short" => [],
                "active" => '',
                "price_shortcut" => '',
                "qty_0_shortcut" => '',
                "categories" => ['tree' => []],
                "id_category_default" => '',
                "related_products" => [],
                "id_manufacturer" => '',
                "features" => [],
                "images" => [],
            ],
            "step2" => [
                "price" => '',
                "ecotax" => '',
                "id_tax_rules_group" => '',
                "on_sale" => '',
                "wholesale_price" => '',
                "unit_price" => '',
                "unity" => '',
                "specific_price" => [],
                "specificPricePriority_0" => '',
                "specificPricePriority_1" => '',
                "specificPricePriority_2" => '',
                "specificPricePriority_3" => ''
            ],
            "step3" => [
                "advanced_stock_management" => '',
                "depends_on_stock" => '',
                "qty_0" => '',
                "combinations" => [],
                "out_of_stock" => '',
                "minimal_quantity" => '',
                "available_now" => [],
                "available_later" => [],
                "available_date" => '',
                "pack_stock_type" => '',
                "virtual_product" => [],
            ],
            "step4" => [
                "width" => '',
                "height" => '',
                "depth" => '',
                "weight" => '',
                "additional_shipping_cost" => '',
                "selectedCarriers" => []
            ],
            "step5" => [
                "link_rewrite" => [],
                "meta_title" => [],
                "meta_description" => [],
            ],
            "step6" => [
                "redirect_type" => '',
                "id_product_redirected" => [],
                "visibility" => '',
                "tags" => [],
                "display_options" => [],
                "upc" => '',
                "ean13" => '',
                "isbn" => '',
                "reference" => '',
                "condition" => '',
                "suppliers" => [],
                "default_supplier" => '',
                "custom_fields" => [],
                "attachments" => [],
                "supplier_combination_1" => []
            ]
        ];
    }
    private function fakeCombination()
    {
        return array('0' => [
            "id_product_attribute" => "6",
            "id_product" => "1",
            "reference" => "",
            "supplier_reference" => "",
            "location" => "",
            "ean13" => "",
            "isbn" => "",
            "upc" => "",
            "wholesale_price" => "0.000000",
            "price" => "0.000000",
            "ecotax" => "0.000000",
            "quantity" => 300,
            "weight" => "0.000000",
            "unit_price_impact" => "0.000000",
            "default_on" => null,
            "minimal_quantity" => "1",
            "available_date" => "0000-00-00",
            "id_shop" => "1",
            "id_attribute_group" => "1",
            "is_color_group" => "0",
            "group_name" => "Taille",
            "attribute_name" => "L",
            "id_attribute" => "3"
        ]);
    }

    private function fakeProduct()
    {
        $product = new \ProductCore();
        $product->name = 'Product name';

        return $product;
    }

    protected function setUp()
    {
        self::$kernel = $this->createKernel();
        self::$kernel->boot();
        $this->container = self::$kernel->getContainer();

        $this->product = $this->fakeProduct();

        $this->adminModelAdapter = new AdminModelAdapter(
            $this->product,
            $this->container->get('prestashop.adapter.legacy.context'),
            $this->container->get('prestashop.adapter.admin.wrapper.product'),
            $this->container->get('prestashop.adapter.tools'),
            $this->container->get('prestashop.adapter.data_provider.product'),
            $this->container->get('prestashop.adapter.data_provider.supplier'),
            $this->container->get('prestashop.adapter.data_provider.warehouse'),
            $this->container->get('prestashop.adapter.data_provider.feature'),
            $this->container->get('prestashop.adapter.data_provider.pack'),
            $this->container->get('prestashop.adapter.shop.context')
        );
    }

    protected function tearDown()
    {
        unset($this->container, $this->product, $this->adminModelAdapter);
        self::$kernel = null;
    }

    /**
     * Checks that the construction of object still works as expected
     */
    public function testConstruct()
    {
        $this->assertInstanceOf('PrestaShopBundle\Model\Product\AdminModelAdapter', $this->adminModelAdapter);
    }

    public function testGetFormData()
    {
        $this->assertInternalType('array', $this->adminModelAdapter->getFormData());
        $expectedArrayStructure = $this->fakeFormData();

        foreach ($expectedArrayStructure as $property => $value) {
            $this->assertArrayHasKey($property, $this->adminModelAdapter->getFormData(), sprintf('The expected key %s was not found', $property));
        }
    }

    public function testGetModelData()
    {
        $this->assertInternalType('array', $this->adminModelAdapter->getModelData($this->fakeFormData()));
    }

    /**
     * @todo find a way to check the value of `attribute_quantity` and `name` and `attribute_price_display` that depend on database
     */
    public function testGetFormCombination()
    {
        $expectedStructureReturn = array(
            "id_product_attribute" => "6",
            "attribute_reference" => "",
            "attribute_ean13" => "",
            "attribute_isbn" => "",
            "attribute_upc" => "",
            "attribute_wholesale_price" => "0.000000",
            "attribute_price_impact" => 0,
            "attribute_price" => "0.000000",
            "final_price" => 0,
            "attribute_priceTI" => "",
            "attribute_ecotax" => "0.000000",
            "attribute_weight_impact" => 0,
            "attribute_weight" => "0.000000",
            "attribute_unit_impact" => 0,
            "attribute_unity" => "0.000000",
            "attribute_minimal_quantity" => "1",
            "available_date_attribute" => "0000-00-00",
            "attribute_default" => false,
            "attribute_quantity" => 300,
            "name" => "Taille - L",
        );
        $combinationDataProvider = new combinationDataProvider();
        $actualReturn = $combinationDataProvider->completeCombination($this->fakeCombination(), $this->product);

        foreach ($expectedStructureReturn as $property => $value) {
            $this->assertArrayHasKey($property, $actualReturn, sprintf('The expected key %s was not found', $property));
            $this->assertEquals($value, $actualReturn[$property], sprintf('The expected value for property %s is wrong', $property));
        }
    }
}
