<?php
class ModelModuleKeepincrm extends Model {
  public function install() {
    $this->load->model('extension/event'); 
    if (version_compare(VERSION, '2.2.0.0') >= 0) {
      $this->model_extension_event->addEvent('keepincrm_add_order_history', 'catalog/model/checkout/order/addOrderHistory/after', 'module/keepincrm/eventAddOrderHistory');
    } else {
      $this->model_extension_event->addEvent('keepincrm_add_order_history', 'post.order.history.add', 'module/keepincrm/eventAddOrderHistory');
    }
  }
  public function uninstall() {
    $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `code` = 'keepincrm'");
    
    $this->load->model('extension/event');
    $this->model_extension_event->deleteEvent('keepincrm_add_order_history');
  }
}