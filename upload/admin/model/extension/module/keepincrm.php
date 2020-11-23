<?php
class ModelExtensionModuleKeepincrm extends Model {
  public function install() {
    if (version_compare(VERSION,'3.0.0.0','>=')) {
      $this->load->model('setting/event');
      $this->model_setting_event->addEvent('keepincrm_add_order_history', 'catalog/model/checkout/order/addOrderHistory/after', 'extension/module/keepincrm/eventAddOrderHistory');
      $this->model_setting_event->addEvent('keepincrm_add_order_history', 'catalog/model/madeshop/order/addOrderHistory/after', 'extension/module/keepincrm/eventAddOrderHistory');

      # NOTE: For current project
      $this->model_setting_event->addEvent('keepincrm_add_minprice', 'catalog/model/madeshop/minprice/addMinprice/after', 'extension/module/keepincrm/eventAddMinprice');
      $this->model_setting_event->addEvent('keepincrm_add_call', 'catalog/model/madeshop/call/addCall/after', 'extension/module/keepincrm/eventAddCall');
    } else {
      $this->load->model('extension/event');
      $this->model_extension_event->addEvent('keepincrm_add_order_history', 'catalog/model/checkout/order/addOrderHistory/after', 'extension/module/keepincrm/eventAddOrderHistory');
    }
  }
  public function uninstall() {
    $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `code` = 'keepincrm'");
    if (version_compare(VERSION,'3.0.0.0','>=')) {
      $this->load->model('setting/event');
      $this->model_setting_event->deleteEventByCode('keepincrm_add_order_history');
      $this->model_setting_event->deleteEventByCode('keepincrm_add_minprice');
      $this->model_setting_event->deleteEventByCode('keepincrm_add_call');

    } else {
      $this->load->model('extension/event');
      $this->model_extension_event->deleteEvent('keepincrm_add_order_history');
    }
  }
}