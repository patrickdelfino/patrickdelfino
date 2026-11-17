<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.9.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' );

global $woocommerce;

$subtotal = $woocommerce->cart->get_subtotal();
$total = $woocommerce->cart->total;

$mensagens = get_theme_mod('stcode_header_message');
?>
<div class="produtos-algc">
    <section>
        <table class="tabela-compras">
            <thead>
                <tr>
                    <th class="LAQKxn"><div class="mcsiKT"><label class="stardust-checkbox"><input class="stardust-checkbox__input" type="checkbox" aria-checked="false" aria-disabled="false" tabindex="0" role="checkbox" aria-label="clique aqui para selecionar todos os produtos"><div class="stardust-checkbox__box"></div></label></div> Produtos</th>
                    <th>Preço Unitário</th>
                    <th>Quantidade</th>
                    <th>Preço Total</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                    $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                    $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
                    /**
                     * Filter the product name.
                     *
                     * @since 2.1.0
                     * @param string $product_name Name of the product in the cart.
                     * @param array $cart_item The product in the cart.
                     * @param string $cart_item_key Key for the product in the cart.
                     */
                    $product_name = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );

                    if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                        $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                        ?>
                        <tr data-item-id="<?php echo $product_id; ?>">
                            <td class="LAQKxn">
                                <div class="lgcEHJ">
                                    <label class="stardust-checkbox">
                                        <input class="stardust-checkbox__input" type="checkbox" aria-checked="false" aria-disabled="false" tabindex="0" role="checkbox" aria-label="clique aqui para selecionar esse produto">
                                        <div class="stardust-checkbox__box"></div>
                                    </label>
                                </div>
                                <div class="eUrDQm">
                                    <div class="LAQKxn">
                                        <?php
                                        $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

                                        if ( ! $product_permalink ) {
                                            echo $thumbnail; // PHPCS: XSS ok.
                                        } else {
                                            printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
                                        }
                                        ?>
                                        <div class="TyNN8t">
                                            <a class="JB57cn" title="<?php echo get_the_title($product_id); ?>" href="<?php echo get_the_permalink($product_id); ?>"><?php echo get_the_title($product_id); ?></a>
                                            <div class="QRuJU-"></div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="singular_price_single"><?php echo wc_price($cart_item['data']->get_price()); ?></td>
                            <td>
                                <div class="qty">
                                    <button onclick="decreaseQuantity(this)"><i class="bx bx-minus"></i></button>
                                    <span id="quantity"><?php echo $cart_item['quantity']; ?></span>
                                    <button onclick="increaseQuantity(this)"><i class="bx bx-plus"></i></button>
                                </div>
                            </td>
                            <td class="total_price_single"><?php echo wc_price($cart_item['data']->get_price()); ?></td>
                            <td><button class="remove-item">Excluir</button></td>
                        </tr><?php 
                    } 
                } ?>
            </tbody>
        </table>
    </section>
    <aside>
        <div class="box">
            <header>Resumo da compra</header>
            <div class="info">
                <div><span>Sub-total</span><span class="count_subtotal_price"><span class="woocommerce-Price-amount amount"><bdi>R$ 00,00</bdi></span></span></div>
                <div><span>Frete</span><span> A Combinar</span></div>
            </div>
            <footer>
                <span>Total</span>
                <span class="count_total_price"><span class="woocommerce-Price-amount amount"><bdi>R$ 00,00</bdi></span></span>
            </footer>
        </div>
        <button id="finesh_product">Finalizar Compra</button>
    </aside>
</div>
<style type="text/css">
.produtos-algc {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
}

.produtos-algc > section {
    -webkit-box-flex: 1;
    -ms-flex: 1;
    flex: 1;
}

.mcsiKT {
    padding: 0 12px 0 20px;
    display: flex;
    flex-direction: row-reverse;
    min-width: 58px;
    box-sizing: border-box;
}

.stardust-checkbox {
    font-family: -apple-system,HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica,Roboto,Droid Sans,Arial,sans-serif;
    font-weight: 300;
    font-size: 12px;
    display: flex;
    align-items: center;
    cursor: default;
    position: relative;
    color: rgba(0,0,0,.54);
    max-width: 400px
}

.stardust-checkbox__input {
    opacity: 0;
    top: 0;
    left: 0;
    position: absolute
}

.stardust-checkbox__box {
    -webkit-user-select: none;
    -moz-user-select: none;
    user-select: none;
    position: relative;
    width: 16px;
    height: 16px;
    flex-shrink: 0;
    border-radius: 2px;
    border: 1px solid rgba(0,0,0,.14);
    text-align: center;
    box-shadow: inset 0 2px 0 0 rgba(0,0,0,.02);
    margin-right: 8px
}

.stardust-checkbox__box:hover {
    box-shadow: inset 0 2px 1px 0 rgba(0,0,0,.09)
}

.stardust-checkbox__box:before {
    position: absolute;
    content: "";
    height: 5px;
    width: 9px;
    border-left: 2px solid #fff;
    border-bottom: 2px solid #fff;
    transform: rotate(-45deg);
    left: 3px;
    top: 3px
}

.stardust-checkbox--disabled {
    opacity: .35;
    cursor: no-drop
}

.stardust-checkbox--disabled .stardust-checkbox__box {
    background: #e7e7e7!important;
    box-shadow: none!important
}

.stardust-checkbox--disabled .stardust-checkbox__box:before {
    border-color: transparent
}

.stardust-checkbox--checked>.stardust-checkbox__box {
    background: #ee4d2d;
    border-color: #ee4d2d
}

.stardust-checkbox--checked>.stardust-checkbox__box:hover {
    box-shadow: none
}

.stardust-checkbox--checked>.stardust-checkbox__box:before {
    border-color: #fff
}

.lgcEHJ {
    position: relative;
    padding: 0 12px 0 20px;
    display: flex;
    flex-direction: row-reverse;
    min-width: 58px;
    box-sizing: border-box;
}

.eUrDQm, .LAQKxn {
    display: flex;
    align-items: center;
}
.eUrDQm {
    width: 49.03811%;
    box-sizing: border-box;
    position: relative;
    flex-direction: column;
}

.eUrDQm img {
    width: 80px;
    height: 80px;
    object-fit: contain;
}

.JB57cn {
    text-decoration: none;
    color: rgba(0,0,0,.87);
    display: block;
    margin-bottom: 5px;
    max-height: 32px;
    text-overflow: ellipsis;
    word-break: break-all;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
}

.JB57cn, .TyNN8t {
    line-height: 16px;
    overflow: hidden;
}

.TyNN8t {
    display: flex;
    flex: 1;
    flex-direction: column;
    padding: 5px 20px 0 10px;
    font-size: 14px;
}

.tabela-compras {
  width: 100%;
  border-collapse: collapse;
}

.tabela-compras thead tr {
    height: 55px;
    line-height: 45px;
    font-size: 14px;
}

.tabela-compras thead tr th {
    text-align: left;
    padding-bottom: 10px;
    text-transform: uppercase;
    font-weight: 500;
}

.tabela-compras tbody tr {
    border-bottom: 3px solid #eee;
}

.tabela-compras tbody tr:last-child {
    border: 0;
}

.tabela-compras tbody tr td {
    padding: 30px 0;
}

.qty {
  background: #eee;
  display: -webkit-inline-box;
  display: -ms-inline-flexbox;
  display: inline-flex;
  padding: 0 0;
  -ms-flex-pack: distribute;
      justify-content: space-around;
  -webkit-box-align: center;
      -ms-flex-align: center;
          align-items: center;
  min-width: 60px;
  border-radius: 20px;
  overflow: hidden;
  height: 30px;
}

.qty span {
  margin: 0 10px;
}

.qty button {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
      -ms-flex-align: center;
          align-items: center;
  background: transparent;
  border: 0;
  padding: 0 10px;
  font-size: 20px;
  height: 100%;
}

.qty button:hover {
  background: #ddd;
}

aside {
    width: 300px;
    margin-left: 30px;
}

aside .box {
  margin-bottom: 15px;
  border: 1px solid #cccccc42;
  background: #eeeeee4f;
  color: #222;
}

aside .box header {
  padding: 15px 20px;
  font-size: 18px;
  border-bottom: 1px solid #e3e3e3;
}

aside .box .info {
  padding: 20px;
  font-size: 16px;
}

aside .box .info > div {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: justify;
      -ms-flex-pack: justify;
          justify-content: space-between;
  margin-bottom: 10px;
  color: #555;
}

aside .box .info > div:last-child {
  margin: 0;
}

aside .box .info button {
  color: #28a745;
  background: transparent;
  border: 0;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
      -ms-flex-align: center;
          align-items: center;
  font-size: 16px;
}

aside .box footer {
  padding: 15px 20px;
  background: #e7efff4a;
  font-size: 18px;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: justify;
  -ms-flex-pack: justify;
  justify-content: space-between;
}

aside > button {
  border: 0;
  padding: 15px 0;
  color: white;
  background: #f77b0e;
  display: block;
  width: 100%;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-size: 16px;
}

aside > button:hover {
  background: #e75d26;
}

.remove-item:hover {
    color: #ee4d2d;
    transition: color .1s cubic-bezier(.4,0,.6,1);
}
</style>
<?php do_action( 'woocommerce_after_cart' ); ?> 