<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-keepincrm" data-toggle="tooltip" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
        <h1><?php echo $heading_title; ?></h1>
        <ul class="breadcrumb">
          <?php foreach ($breadcrumbs as $breadcrumb) { ?>
            <li>
              <a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
            </li>
          <?php } ?>
        </ul>
      </div>
    </div>
    <div class="container-fluid">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $k_settings; ?></h3>
        </div>
        <div class="panel-body">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $k_tab_general; ?></a></li>
            <li><a href="#tab-log" data-toggle="tab"><?php echo $k_tab_log; ?></a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-keepincrm" class="form-horizontal">
                <h3 style="margin-top: 25px; margin-bottom: 25px;"><?php echo $h_basic_settings; ?></h3>
                <div class="form-group required">
                  <label class="col-sm-2 control-label" for="input-key" >
                    <span data-toggle="tooltip" title="" data-original-title="<?php echo $d_api_key; ?>"><?php echo $k_api_key; ?></span>
                  </label>
                  <div class="col-sm-10">
                    <input type="text" name="keepincrm_key" value="<?php echo $keepincrm_key; ?>" placeholder="<?php echo $k_api_key; ?>" id="input-key" class="form-control" />
                    <?php if ($error_key) { ?>
                      <div class="text-danger"><?php echo $error_key; ?></div>
                    <?php } ?>
                  </div>
                </div>
                <?php if (!$error_key || $keepincrm_key) { ?>
                  <div class="form-group" >
                    <label class="col-sm-2 control-label" for="input-status">
                      <span data-toggle="tooltip" title="" data-original-title="<?php echo $d_data_types; ?>"><?php echo $k_data_types; ?></span>
                    </label>
                    <div class="col-sm-10">
                      <select name="keepincrm_status" id="input-status" class="form-control">
                        <?php if ($keepincrm_status) { ?>
                          <option value="0" selected="selected"><?php echo $k_lead; ?></option>
                          <option value="1"><?php echo $k_client; ?></option>
                        <?php } else { ?>
                          <option value="0"><?php echo $k_lead; ?></option>
                          <option value="1" selected="selected"><?php echo $k_client; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-ignore_price_list">
                      <span data-toggle="tooltip" title="" data-original-title="<?php echo $d_ignore_price_list; ?>"><?php echo $k_ignore_price_list; ?></span>
                    </label>
                    <div class="col-sm-10">
                      <select name="keepincrm_ignore_price_list" id="input-ignore_price_list" class="form-control">
                        <?php if ($keepincrm_ignore_price_list) { ?>
                          <option value="1" selected="selected"><?php echo $k_false; ?></option>
                          <option value="0"><?php echo $k_true; ?></option>
                        <?php } else { ?>
                          <option value="1"><?php echo $k_false; ?></option>
                          <option value="0" selected="selected"><?php echo $k_true; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-products_total_as_total">
                      <span data-toggle="tooltip" title="" data-original-title="<?php echo $d_products_total_as_total; ?>"><?php echo $k_products_total_as_total; ?></span>
                    </label>
                    <div class="col-sm-10">
                      <select name="keepincrm_products_total_as_total" id="input-products_total_as_total" class="form-control">
                        <?php if ($keepincrm_products_total_as_total) { ?>
                          <option value="1" selected="selected"><?php echo $k_true; ?></option>
                          <option value="0"><?php echo $k_false; ?></option>
                        <?php } else { ?>
                          <option value="1"><?php echo $k_true; ?></option>
                          <option value="0" selected="selected"><?php echo $k_false; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-source">
                      <span data-toggle="tooltip" title="" data-original-title="<?php echo $d_source; ?>"><?php echo $k_source; ?></span>
                    </label>
                    <div class="col-sm-10">
                      <input type="number" name="keepincrm_source" value="<?php echo $keepincrm_source; ?>" placeholder="<?php echo $k_source; ?>" id="input-source" class="form-control" />
                      <?php if (!$keepincrm_source) { ?>
                        <div class="text-danger"><?php echo $field_required; ?></div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-user_id">
                      <span data-toggle="tooltip" title="" data-original-title="<?php echo $d_user_id; ?>"><?php echo $k_user_id; ?></span>
                    </label>
                    <div class="col-sm-10">
                      <input type="number" name="keepincrm_user_id" value="<?php echo $keepincrm_user_id; ?>" placeholder="<?php echo $k_user_id; ?>" id="input-user_id" class="form-control" />
                      <?php if (!$keepincrm_user_id) { ?>
                        <div class="text-danger"><?php echo $field_required; ?></div>
                      <?php } ?>
                    </div>
                  </div>

                  <h3 style="margin-top: 25px; margin-bottom: 25px;"><?php echo $h_custom_fild; ?></h3>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-address">
                      <span data-toggle="tooltip" title="" data-original-title="<?php echo $d_address; ?>"><?php echo $k_address; ?></span>
                    </label>
                    <div class="col-sm-10">
                      <input type="number" name="keepincrm_address" value="<?php echo $keepincrm_address; ?>" placeholder="<?php echo $k_address; ?>" id="input-address" class="form-control" />
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-payment">
                      <span data-toggle="tooltip" title="" data-original-title="<?php echo $d_payment; ?>"><?php echo $k_payment; ?></span>
                    </label>
                    <div class="col-sm-10">
                      <input type="number" name="keepincrm_payment" value="<?php echo $keepincrm_payment; ?>" placeholder="<?php echo $k_payment; ?>" id="input-payment" class="form-control" />
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-delivery">
                      <span data-toggle="tooltip" title="" data-original-title="<?php echo $d_delivery; ?>"><?php echo $k_delivery; ?></span>
                    </label>
                    <div class="col-sm-10">
                      <input type="number" name="keepincrm_delivery" value="<?php echo $keepincrm_delivery; ?>" placeholder="<?php echo $k_delivery; ?>" id="input-delivery" class="form-control" />
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-coupon">
                      <span data-toggle="tooltip" title="" data-original-title="<?php echo $d_product_details; ?>"><?php echo $k_product_details; ?></span>
                    </label>
                    <div class="col-sm-10">
                      <input type="number" name="keepincrm_product_details" value="<?php echo $keepincrm_product_details; ?>" placeholder="<?php echo $k_product_details; ?>" id="input-coupon" class="form-control" />
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-coupon">
                      <span data-toggle="tooltip" title="" data-original-title="<?php echo $d_coupon; ?>"><?php echo $k_coupon; ?></span>
                    </label>
                    <div class="col-sm-10">
                      <input type="number" name="keepincrm_coupon" value="<?php echo $keepincrm_coupon; ?>" placeholder="<?php echo $k_coupon; ?>" id="input-coupon" class="form-control" />
                    </div>
                  </div>

                  <h3 style="margin-top: 25px; margin-bottom: 25px;"><?php echo $h_xml; ?></h3>
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-store_name">
                      <span data-toggle="tooltip" title="" data-original-title="<?php echo $d_store_name; ?>"><?php echo $k_store_name; ?></span>
                    </label>
                    <div class="col-sm-10">
                      <input type="text" name="keepincrm_store_name" value="<?php echo $keepincrm_store_name; ?>" placeholder="<?php echo $k_store_name; ?>" id="input-coupon" class="form-control" />
                      <?php if (!$keepincrm_store_name) { ?>
                        <div class="text-danger"><?php echo $field_required; ?></div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-company_name">
                      <span data-toggle="tooltip" title="" data-original-title="<?php echo $d_company_name; ?>"><?php echo $k_company_name; ?></span>
                    </label>
                    <div class="col-sm-10">
                      <input type="text" name="keepincrm_company_name" value="<?php echo $keepincrm_company_name; ?>" placeholder="<?php echo $k_company_name; ?>" id="input-coupon" class="form-control" />
                      <?php if (!$keepincrm_company_name) { ?>
                        <div class="text-danger"><?php echo $field_required; ?></div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-url_xml">
                      <span data-toggle="tooltip" title="" data-original-title="<?php echo $d_url_xml; ?>"><?php echo $k_url_xml; ?></span>
                    </label>
                    <div class="col-sm-10">
                      <input type="text" name="keepincrm_url_xml" value="<?php echo $keepincrm_url_xml; ?>" disabled="disabled" id="input-coupon" class="form-control" />
                    </div>
                  </div>
                <?php } ?>
              </form>
            </div>
            <div class="tab-pane" id="tab-log">
              <p>
                <textarea wrap="off" rows="15" class="form-control"> <?php echo $log; ?> </textarea>
              </p>
              <div class="text-right"><a href="<?php echo $clear_log; ?>" class="btn btn-danger"><i class="fa fa-eraser"></i> <?php echo $k_button_clear; ?></a></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div style="text-align: center; margin-bottom: 20px;">
    <p>Version: 3.0.2</p>
    <p>Made by KeepinCRM team. <a href="https://keepincrm.com" target="_blank">https://keepincrm.com</a></p>
  </div>
  <?php echo $footer; ?>