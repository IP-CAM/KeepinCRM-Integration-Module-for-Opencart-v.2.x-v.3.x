<?php
class ControllerModuleKeepincrm extends Controller {
  public function eventAddOrderHistory($route, $vdata = '', $order_id = '') {
    if (version_compare(VERSION, '2.2.0.0') >= 0) {
      $order_id = $order_id ? $order_id : $vdata[0];
    } else {
      $order_id = $route;
    }
    $this->load->model('checkout/order'); 
    $this->load->model('account/order');
    $this->load->model('catalog/product');
    
    if (count($this->model_account_order->getOrderHistories($order_id)) > 1) {
      return;
    }

    $order = $this->model_checkout_order->getOrder($order_id);
    $order_products = $this->model_account_order->getOrderProducts($order_id);
    $order_totals = $this->model_account_order->getOrderTotals($order_id);

    $keepincrm_key = $this->config->get('keepincrm_key');
    $keepincrm_status = $this->config->get('keepincrm_status');
    $keepincrm_source = $this->config->get('keepincrm_source');
    $keepincrm_ignore_price_list = $this->config->get('keepincrm_ignore_price_list');
    $keepincrm_products_total_as_total = $this->config->get('keepincrm_products_total_as_total');
    $keepincrm_user_id = $this->config->get('keepincrm_user_id');

    $keepincrm_address_field = $this->config->get('keepincrm_address');
    $keepincrm_coupon_field = $this->config->get('keepincrm_coupon');
    $keepincrm_payment_field = $this->config->get('keepincrm_payment');
    $keepincrm_delivery_field = $this->config->get('keepincrm_delivery');
    $keepincrm_product_details_field = $this->config->get('keepincrm_product_details');

    # NOTE: move all this vars to $data['var_name']
    $cupon = '';
    $address = '';
    $shipping = '';
    $url = '';
    $ipl = '';

    foreach ($order_totals as $tot) {
      if ($tot["code"] == 'coupon') {
        $cupon = $tot["title"].' '.$tot["value"].'; ';
      }
      if ($tot["code"] == 'productbundlestotal2') {
        $cupon = $tot["title"].' '.$tot["value"].'; ';
      }
      if ($tot["code"] == 'shipping') {
        $shipping = $tot["title"].' '.$tot["value"].'; ';
      }
    }

    if (isset($order['shipping_method'])) {
      $shipping = $order['shipping_method'];
    }
    
    // Lead
    if ($keepincrm_status == '0') {
      $lead = true;
    } else {
      $lead = false;
    }
    
    if ($keepincrm_ignore_price_list == '0') {
      $ipl = '?ignore_price_list=true';
    }   
    if ($keepincrm_products_total_as_total == '0') {
      if ($ipl == '?ignore_price_list=true') {
        $ipl .= '&products_total_as_total=false';
      } else {
        $ipl .= '?products_total_as_total=false';
      } 
    }
    
    // Address
    if ($order["payment_country"]) {
      $address .= 'Страна: '. $order["payment_country"].'; ';
    };
    if ($order["payment_zone"]) {
      $address .= 'Регион: '. $order["payment_zone"].'; ';
    };
    if ($order["payment_city"]) {
      $address .= 'Город: '. $order["payment_city"].'; ';
    };
    if ($order["payment_address_1"]) {
      $address .= 'Адрес: '. $order["payment_address_1"].'; ';
    };
    if ($order["payment_address_2"]) {
      $address .= 'Адрес: '. $order["payment_address_2"].' ';
    };

    $i = 0;
    $products_list = array();

    foreach ($order_products as $product) {
      if (isset($product['tax_class_id'])) {
        $price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
        $price  = str_replace(' грн', '', $price);
      } else {
        $price = $product['price'];
      }
      
      $product_info = $this->model_catalog_product->getProduct($product['product_id']);
      $url .= $this->url->link('product/product', 'product_id=' . $product['product_id']).'; ';
      $product_options = $this->model_account_order->getOrderOptions($order_id, $product['order_product_id']);
      $optionstit = '';

      foreach ($product_options as $options) {
        $optionstit .= ' '.$options['name'].'-'.$options['value'].';';
      }
      if ($optionstit) {
        $products_list[$i] = array (
          'amount'              => $product["quantity"],
          'title'               => $product["name"].' '.$optionstit,
          'product_attributes'  => array (
            'title'             => $product["name"].' '.$optionstit,
            'price'             => $price 
          )  
        ); 
      } else {
        $products_list[$i] = array (
          'amount'              => $product["quantity"],
          'title'               => $product["name"],
          'product_attributes'  => array (
            'sku'               => $product_info["sku"],
            'title'             => $product["name"],
            'price'             => $price 
          )  
        ); 
      }
      $i++;
    };

    // Custom_fields
    $i = 0;
    $custom_fields = array();
    if ($keepincrm_delivery_field) {
      $custom_fields[$i] = array (
        'name'          => "field_".$keepincrm_delivery_field,
        'value'         => $shipping
      );
      $i++;
    }
    if ($keepincrm_address_field) {
      $custom_fields[$i] = array (
        'name'          => "field_".$keepincrm_address_field,
        'value'         => $address,
      );
      $i++;
    }
    if ($keepincrm_payment_field) {
      $custom_fields[$i] = array (
        'name'          => "field_".$keepincrm_payment_field,
        'value'         => $order['payment_method']
      );
      $i++;
    }
    if ($keepincrm_product_details_field) {
      $custom_fields[$i] = array (
        'name'          => "field_".$keepincrm_product_details_field,
        'value'         => $url
      );
      $i++;
    }
    if ($keepincrm_coupon_field) {
      $custom_fields[$i] = array (
        'name'          => "field_".$keepincrm_coupon_field,
        'value'         => $cupon
      );
      $i++;
    }

    $email = $order["email"];
    $search = 'empty';
    if (preg_match("/{$search}/i", $email)) {
      $email = '';
    }

    if (isset($product['tax_class_id'])) {
      $totals = $this->currency->format($this->tax->calculate($order["total"], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
    } else {
      $totals = $order["total"];
    }

    if ($address) {
      $title = '№ '.$order_id;
    } else {
      $title = 'Быстрый заказ - '.$order_id;
    }

    $order_details = array (
      'title'                 => $title,
      'comment'               => $order['comment'],
      'total'                 => $totals,
      'main_responsible_id'   => $keepincrm_user_id,
      'source_id'             => $keepincrm_source,
      'client_attributes'     => array (
        'person'              => $order["firstname"].' '.$order["lastname"],
        'email'               => $email,
        'lead'                => $lead,
        'source_id'           => $keepincrm_source,
        'phones'              => array (
          0                   => $order["telephone"],
        ),
      ),
      'jobs_attributes'       => $products_list,
      'custom_fields'         => $custom_fields
    );

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://api.keepincrm.com/v1/agreements'.$ipl);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json', 'X-Auth-Token: '.$keepincrm_key.'','Content-Type: application/json'));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($order_details));

    $response = curl_exec($curl);
    $info = curl_getinfo($curl);
    $response = print_r($response, TRUE);
    $order_details = print_r($order_details, TRUE);
    $http_code = print_r($info["http_code"], TRUE);
    curl_close($curl);

    $find = array("\n", " ");
    $file = DIR_LOGS ."keepincrm.log";
    $log = date('Y-m-d H:i:s') . ' - ';
    $log .= str_replace($find, "", $http_code).' - ';
    $log .= str_replace($find, "", $response).' - ';
    $log .= str_replace($find, "", $order_details);

    if (!file_exists($file)) {
      $fp = fopen($file, "w");
      file_put_contents($file, $log . PHP_EOL, FILE_APPEND);
      fclose($fp);
    } else {
      $filedata = file($file, FILE_IGNORE_NEW_LINES);
      file_put_contents($file, $log . PHP_EOL, FILE_APPEND);
    }
  }

  public function import_xml() {
    set_time_limit(300);
    error_reporting(0);
    
    header('Content-Description: File Transfer');
    header("Content-Disposition: attachment; filename=keepincrm.xml");
    header("Content-Type: application/xml; charset=utf-8");
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    ini_set('output_buffering', 'Off');
    ini_set('zlib.output_compression', 0);
    ini_set('implicit_flush', 1);
    ob_clean();

    if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
      $site_url = $this->config->get('config_ssl');
    } else {
      $site_url = $this->config->get('config_url');
    }
    $site_url = rtrim($site_url, "/");

    $today = date("Y-m-d H:i:s");

    $currency = $this->config->get('config_currency');
    $company_name = $this->config->get('keepincrm_company_name');
    $store_name = $this->config->get('keepincrm_store_name');

    $this->load->model('catalog/category');
    $this->load->model('catalog/product');
    $this->load->model('tool/image');

    ob_end_flush();
    ob_start();
    echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
    ob_flush();
    flush();

    echo("<yml_catalog date=\"$today\">\n");
    echo("\t<shop>\n");
    echo("\t\t<platform>KeepinCRM</platform>\n");
    echo("\t\t<name>$store_name</name>\n");
    echo("\t\t<company>$company_name</company>\n");
    echo("\t\t<url>$site_url</url>\n");

    $data_categories = array();
    echo("\t\t<categories>\n");

    $categories = $this->db->query("SELECT DISTINCT category_id, parent_id, name, language_id FROM ".DB_PREFIX."category INNER JOIN ".DB_PREFIX."category_description USING(category_id)");
    $data_categoriesorting = array();
    $data_categories = array();

    foreach ($categories->rows as $categori) {
      $id = $categori['category_id'];
      $data_categories[$id] = array(
        'id'                => $categori['category_id'], 
        'parent_id'         => $categori['parent_id'],
        'name'              => $categori['name']
      );
    }

    function foo($parent_id, $data_categories, $data_categoriesper) {
      $name = trim($data_categories[$parent_id]["name"]);
      $id = $data_categories[$parent_id]["id"];
      $parent_id = $data_categories[$parent_id]["parent_id"];

      if (in_array($id, $GLOBALS['data_categoriesper'])) {
      } else {
        $GLOBALS['data_categoriesper'][]= $id;
        if ($parent_id !='0') {
          $GLOBALS['data_categoriesorting'][$id] = array(
            'id'            => $id, 
            'parent_id'     => $parent_id,
            'name'          => $name
          );
        } else {
          $GLOBALS['data_categoriesorting'][$id] = array(
            'id'            => $id, 
            'parent_id'     => '0',
            'name'          => $name
          ); 
        }
        if ($parent_id != '0') {
          foo($parent_id, $data_categories, $data_categoriesper);
        }  
      }
    }

    $categories = $this->db->query("SELECT product_id FROM ".DB_PREFIX."product_to_category ");
    $data_categoriesper = array();
    $this->load->model('catalog/product');
    $this->load->model('catalog/category');

    foreach ($categories->rows as $categori) {
      $product_id = $categori['product_id'];
      $product_cat = $this->model_catalog_product->getCategories($product_id);
      $product_cat_parent = $this->model_catalog_category->getCategory($product_cat[0]['category_id']);
      $category_id = $product_cat_parent["category_id"];
      $parent_id = $product_cat_parent["parent_id"];
      $name = $product_cat_parent["name"];
      if (array_key_exists($category_id, $GLOBALS['data_categoriesorting'])) {
      } else {
        if ($parent_id != '0') {
          foo($parent_id, $data_categories, $data_categoriesper); 
        }
      }
    }

    foreach ($categories->rows as $categori) {
      $product_id= $categori['product_id'];
      $product_cat = $this->model_catalog_product->getCategories($product_id);
      $product_cat_parent = $this->model_catalog_category->getCategory($product_cat[0]['category_id']);
      $category_id = $product_cat_parent["category_id"];
      $parent_id = $product_cat_parent["parent_id"];
      $name = $product_cat_parent["name"];

      if (array_key_exists($category_id, $GLOBALS['data_categoriesorting'])) {
      } else {
        if ($parent_id) {
          $GLOBALS['data_categoriesorting'][$category_id] = array(
            'id'             => $category_id, 
            'parent_id'      => $parent_id,
            'name'           => $name
          );
        } else {
          $GLOBALS['data_categoriesorting'][$category_id] = array(
            'id'            => $category_id, 
            'parent_id'     => '0',
            'name'          => $name
          ); 
        }
      } 
    }

    foreach ($GLOBALS['data_categoriesorting'] as $categori) {
      $name = trim($categori['name']);
      $id = $categori['id'];
      $parent_id = $categori['parent_id'];
      if ($parent_id) {
      } else {
        if ($id) {
          echo("\t\t\t<category id=\"$id\">$name</category>\n");
        }
      }
    }

    foreach ($GLOBALS['data_categoriesorting'] as $categori) {
      $name = trim($categori['name']);
      $id = $categori['id'];
      $parent_id = $categori['parent_id'];
      if ($parent_id) {
        echo("\t\t\t<category id=\"$id\" parentId=\"$parent_id\">$name</category>\n");
      }
    }
    echo("\t\t</categories>\n");
    echo("\t\t<offers>\n");

    $products = $this->db->query("SELECT DISTINCT product_id, name, description, quantity, image, price, sku, weight, width, length, height FROM ".DB_PREFIX."product INNER JOIN ".DB_PREFIX."product_description USING(product_id)");
    //$products = $this->model_catalog_product->getProducts(array());
    
    $this->load->model('catalog/product');
    $this->load->model('catalog/category');

    foreach ($products->rows as $k => $product_info) {
      $product_id = $product_info['product_id'];
      $product_cat = $this->model_catalog_product->getCategories($product_id);
      $category_id = $product_cat[0]['category_id'];
      $product_cat_parent = $this->model_catalog_category->getCategory($category_id);

      $productimages = $this->model_catalog_product->getProductImages($product_id);
      $url = $this->url->link('product/product', 'product_id=' . $product_id);
      $name = $product_info['name'];
      $name = htmlspecialchars($name);
      $quantity = $product_info['quantity'];
      $image = $product_info['image'];
      $price = $product_info['price'];
      $description = $product_info['description'];
      $sku = $product_info['sku'];
      $weight = $product_info['weight'];
      $width = $product_info['width'];
      $length = $product_info['length'];
      $height = $product_info['height'];

      if ($quantity > 0) {
        echo("\t\t\t<offer id=\"$product_id\" available=\"true\">\n");
      } else {
        echo("\t\t\t<offer id=\"$product_id\" available=\"false\">\n");
      }

      echo("\t\t\t\t<name>$name</name>\n");
      echo("\t\t\t\t<url>$url</url>\n");
      echo("\t\t\t\t<price>$price</price>\n");
      echo("\t\t\t\t<currencyId>$currency</currencyId>\n");
      echo("\t\t\t\t<description><![CDATA[$description]]></description>\n");

      if ($product_cat_parent) {
        echo("\t\t\t\t<categoryId>$category_id</categoryId>\n");
      }

      echo("\t\t\t\t<picture>$site_url/image/$image</picture>\n");

      foreach ($productimages as $images) {
        $picture = $images['image'];
        $pos = strrpos($picture, '/') + 1;
        $picture = substr($picture, 0, $pos) . urlencode(substr($picture, $pos));
        if ($picture) {
          echo("\t\t\t\t<picture>$site_url/image/$picture</picture>\n");
        }
      }
      echo("\t\t\t\t<stock_quantity>$quantity</stock_quantity>\n");

      $product_attributes = $this->model_catalog_product->getProductAttributes($product_info['product_id']);
      if ($sku) {
        echo("\t\t\t\t<param name=\"sku\">$sku</param>\n");
      }
      if ($weight != '0.00') {
        echo("\t\t\t\t<param name=\"weight\">$weight</param>\n"); 
      }
      if ($width != '0.00') {
        echo("\t\t\t\t<param name=\"width\">$width</param>\n");
      }
      if ($length != '0.00') {
        echo("\t\t\t\t<param name=\"length\">$length</param>\n");
      }
      if ($height != '0.00') {
        echo("\t\t\t\t<param name=\"height\">$height</param>\n");
      }
      foreach($product_attributes as $attribute_group) {
        foreach($attribute_group["attribute"] as $attribute) {
          $name = trim($attribute['name']);
          $name = htmlspecialchars($name);
          $text = htmlspecialchars(trim($attribute['text']));
          echo("\t\t\t\t<param name=\"$name\">$text</param>\n");
        }
      }
      echo("\t\t\t</offer>\n");
      ob_flush();
      flush();
    }
    echo("\t\t</offers>\n");
    echo("\t</shop>\n");
    echo("</yml_catalog>");

    ob_flush();
    flush();
  }
}
