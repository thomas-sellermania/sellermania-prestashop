<?php
/*
* 2010 - 2013 Sellermania / 23Prod SARL
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to fabien@23prod.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade your module to newer
* versions in the future.
*
*  @author Fabien Serny - 23Prod <fabien@23prod.com>
*  @copyright	2010-2013 23Prod SARL
*  @version		1.0
*  @license		http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

if (!defined('_PS_VERSION_'))
	exit;

// Load ImportOrder Controller
require_once(dirname(__FILE__).'/SellerManiaImportOrder.php');

class SellerManiaDisplayAdminOrderController
{
	/**
	 * @var private array conditions
	 */
	private $conditions_list = array();

	/**
	 * @var private array status
	 */
	private $status_list = array();

	/**
	 * Controller constructor
	 */
	public function __construct($module, $dir_path, $web_path)
	{
		$this->module = $module;
		$this->web_path = $web_path;
		$this->dir_path = $dir_path;
		$this->context = Context::getContext();

		$this->conditions_list = array(
			0 => $this->module->l('Unknown'),
			1 => $this->module->l('Like new'),
			2 => $this->module->l('Very good'),
			3 => $this->module->l('Good'),
			4 => $this->module->l('Acceptable'),
			5 => $this->module->l('Collectible like new'),
			6 => $this->module->l('Collectible very good'),
			7 => $this->module->l('Collectible good'),
			8 => $this->module->l('Collectible acceptable'),
			10 => $this->module->l('Refurbished mint'),
			11 => $this->module->l('New'),
			12 => $this->module->l('New OEM'),
			13 => $this->module->l('Used openbox'),
		);

		$this->status_list = array(
			6 => $this->module->l('To be confirmed'),
			10 => $this->module->l('Awaiting confirmation'),
			9 => $this->module->l('Confirmed'),
			3 => $this->module->l('Cancelled by the customer'),
			4 => $this->module->l('Cancelled by the seller'),
			1 => $this->module->l('To dispatch'),
			5 => $this->module->l('Awaiting dispatch'),
			2 => $this->module->l('Dispatched'),
		);
	}

	/**
	 * Refresh order
	 * @param string $order_id
	 * @return mixed array data
	 */
	public function refreshOrder($order_id)
	{
		// Retrieving data
		$client = new Sellermania\OrderClient();
		$client->setEmail(Configuration::get('SM_ORDER_EMAIL'));
		$client->setToken(Configuration::get('SM_ORDER_TOKEN'));
		$client->setEndpoint(Configuration::get('SM_ORDER_ENDPOINT'));
		$result = $client->getOrderById($order_id);

		// Preprocess data
		$controller = new SellerManiaImportOrderController();
		$controller->data = $result['SellermaniaWs']['GetOrderResponse']['Order'];
		$controller->preprocessData();

		// Saving it
		$id_sellermania_order = Db::getInstance()->getValue('SELECT `id_sellermania_order` FROM `'._DB_PREFIX_.'sellermania_order` WHERE `id_order` = '.(int)Tools::getValue('id_order'));
		$sellermania_order = new SellermaniaOrder($id_sellermania_order);
		$sellermania_order->info = json_encode($controller->data);
		$sellermania_order->date_accepted = NULL;
		$sellermania_order->update();

		// Return data
		return $controller->data;
	}

	/**
	 * Run method
	 * @return string $html
	 */
	public function run()
	{
		// Check if credentials are ok
		if (Configuration::get('SM_CREDENTIALS_CHECK') != 'ok' || Configuration::get('SM_IMPORT_ORDERS') != 'yes' || Configuration::get('SM_DEFAULT_PRODUCT_ID') < 1)
			return '';

		// Retrieve order data
		$sellermania_order = Db::getInstance()->getValue('SELECT `info` FROM `'._DB_PREFIX_.'sellermania_order` WHERE `id_order` = '.(int)Tools::getValue('id_order'));
		if (empty($sellermania_order))
			return '';

		// Decode order data
		$sellermania_order = json_decode($sellermania_order, true);

		// Refresh order ID
		$sellermania_order = $this->refreshOrder($sellermania_order['OrderInfo']['OrderId']);

		$this->context->smarty->assign('sellermania_order', $sellermania_order);
		$this->context->smarty->assign('sellermania_module_path', $this->web_path);
		$this->context->smarty->assign('sellermania_status_list', $this->status_list);
		$this->context->smarty->assign('sellermania_conditions_list', $this->conditions_list);

		return $this->module->compliantDisplay('displayAdminOrder.tpl');
	}
}
